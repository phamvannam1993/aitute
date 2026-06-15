<?php

namespace App\Services;

use App\Models\AiAssistant;
use App\Models\AiAssistantCriteria;
use App\Models\StepAi;
use App\Repositories\AiAssistantRepository;
use App\Repositories\OccupationRepository;
use App\Repositories\OperationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AiAssistantService
{
    protected $aiAssistantRepository;
    protected $occupationRepository;
    protected $operationRepository;

    // Inject OccupationRepository vào service
    public function __construct(AiAssistantRepository $aiAssistantRepository, OccupationRepository $occupationRepository, OperationRepository $operationRepository)
    {
        $this->occupationRepository = $occupationRepository;
        $this->operationRepository = $operationRepository;
        $this->aiAssistantRepository = $aiAssistantRepository;
    }

    public function create($data, $request)
    {
        $imagePath = null;
        if ($request->hasFile('thumbnail')) {
            try {
                $image = $request->file('thumbnail');
                $imagePath = $image->store(config('constants.s3_folder.ai_assistants'), 's3');
            } catch (\Exception $e) {
                return back()->withErrors(['thumbnail' => 'Không thể tải lên ảnh. Vui lòng thử lại.'])->withInput();
            }
        }

        $data['thumbnail'] = $imagePath;
        $data['admin_id'] = auth('admin')->id();
            DB::transaction(function () use ($request, $data) {
            try {
                $aiAssistant = $this->aiAssistantRepository->create($data);
                // Lưu các steps vào bảng step_ais
                $step_ais = $data['step_ais'];
                foreach ($step_ais as $index => $step) {
                    StepAi::create([
                        'ai_assistant_id' => $aiAssistant->id,
                        'step_name' => $step['name'],
                        'step_description' => $step['description'],
                        'position' => $index + 1
                    ]);
                }
                // Lưu các tiêu chí nếu có
                if ($request->has('criteria')) {
                    foreach ($request->input('criteria') as $criterion) {
                        // Kiểm tra kiểu tiêu chí và xử lý value
                        $value = $criterion['type'] === 'select'
                            ? json_encode($criterion['value'])
                            : $criterion['value'];
                        $aiAssistant->criteria()->create([
                            'name' => $criterion['name'],
                            'type' => $criterion['type'],
                            'value' => $criterion['type'] === 'select' ? json_encode($criterion['value']) : '' ,
                            'placeholder' => $criterion['type'] == 'input' ? $criterion['placeholder'] ?? '' : '',
                            'label' => $criterion['label'] ?? '',
                            'selectedValue' => $criterion['selectedValue'] ?? '',
                            'multiple' => filter_var($criterion['multiple'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0
                        ]);
                    }
                }

            } catch (\Exception $e) {
                \Log::error('Lỗi khi tạo AI:', ['error' => $e->getMessage()]);
                return back()->withErrors(['image' => 'Lỗi khi tạo Ai'])->withInput();
            }
        });

    }

    public function updateAssistant($id, Request $request)
    {
        $operation = $this->aiAssistantRepository->findByIdAndUser($id, auth('admin')->id());
        $data = $request->only(['name','type' ,'description', 'occupation_id', 'operation_id', 'step_ais' ,'criteria']);
        $data['is_public'] = $request->boolean('is_public');
        // Xử lý ảnh nếu có
        if ($request->hasFile('thumbnail')) {
            try {
                // Xóa ảnh cũ từ S3 nếu có
                if ($operation->thumbnail) {
                    Storage::disk('s3')->delete($operation->thumbnail);
                }

                // Lưu ảnh mới vào S3
                $path = $request->file('thumbnail')->store(config('constants.s3_folder.ai_assistants'), 's3');

                $data['thumbnail'] = $path;

            } catch (\Exception $e) {
                // Nếu có lỗi xảy ra trong quá trình upload lên S3
                return back()->withErrors(['image' => 'Không thể tải lên ảnh. Vui lòng thử lại.'])->withInput();
            }
        }

        DB::transaction(function () use ($id, $data) {
            try {
                $steps = $data['step_ais'] ?? [];
                $criteria = $data['criteria'] ?? [];
                // Cập nhật AI Assistant
                $this->aiAssistantRepository->update($id, $data);
                $existingSteps = StepAi::where('ai_assistant_id', $id)->get()->keyBy('id');

                foreach ($steps as $index => $step) {
                    $position = $index + 1;
                    if (isset($step['id']) && isset($existingSteps[$step['id']])) {
                        // Nếu step đã tồn tại, cập nhật step đó
                        $existingSteps[$step['id']]->update([
                            'step_name' => $step['name'],
                            'step_description' => $step['description'],
                            'position' => $position,
                        ]);

                        // Xóa step này khỏi danh sách đã tồn tại để sau đó xóa những step không được cập nhật
                        $existingSteps->forget($step['id']);
                    } else {
                        // Nếu step không có ID, tạo mới step
                        StepAi::create([
                            'ai_assistant_id' => $id,
                            'step_name' => $step['name'],
                            'step_description' => $step['description'],
                            'position' => $position, // Gán vị trí cho step mới
                        ]);
                    }
                }

                // Xóa các step còn lại không có trong request (các step không còn được cập nhật)
                foreach ($existingSteps as $step) {
                    $step->delete();
                }

                // Xóa tất cả các tiêu chí và chèn lại từ đầu
                AiAssistantCriteria::where('ai_assistant_id', $id)->delete();
                foreach ($criteria as $criterion) {
                    $data = [
                        'ai_assistant_id' => $id,
                        'name' => $criterion['name'],
                        'type' => $criterion['type'],
                        'value' => $criterion['type'] === 'select' ? json_encode($criterion['value']) : '' ,
                        'placeholder' => $criterion['type'] == 'input' ? $criterion['placeholder'] ?? '' : '',
                        'label' => $criterion['label'] ?? '',
                        'selectedValue' => $criterion['selectedValue'] ?? '',
                        'multiple' => filter_var($criterion['multiple'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0
                    ];

                    AiAssistantCriteria::create($data);
                }

            } catch (\Exception $e) {
                \Log::error('Lỗi khi update AI:', ['error' => $e->getMessage()]);
                return back()->withErrors(['error' => 'Lỗi khi update AI'])->withInput();
            }
        });

    }

    public function getAssistants(array $filters, int $userId)
    {
        return $this->aiAssistantRepository->getAssistants($filters, $userId);
    }

    public function getAssistantById(int $id)
    {
        return $this->aiAssistantRepository->findByIdAndUser($id, auth('admin')->id());
    }

    function aiAssistantById($id)
    {
        try {
            $ai_assistants = AiAssistant::with('criteria')
                ->where('id', '=', $id)
                ->first();
            return $ai_assistants;
        } catch (\Throwable $e) {
            Log::error('Error deleting message:', ['error' => $e->getMessage()]);
        }
    }

    public function getOccupationById(int $id)
    {
        return $this->occupationRepository->findById($id);
    }

    public function getOperationById(int $id)
    {
        return $this->operationRepository->findById($id);
    }

    public function getListAiTop($userId) {
        return $this->aiAssistantRepository->getListAiTop($userId);
    }

    public function getCount() {
        return $this->aiAssistantRepository->getCount(auth('admin')->id());
    }

    public function getListTimeLine() {
        return $this->aiAssistantRepository->getListTimeLine();
    }

    public function listAis($id) {
        return $this->aiAssistantRepository->listAis($id);
    }

    public function listAisByOccupationId($id) {
        return $this->aiAssistantRepository->listAisByOccupationId($id);
    }


}
