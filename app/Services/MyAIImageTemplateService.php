<?php

namespace App\Services;

use App\Exceptions\DomainException;
use App\Repositories\MyAIImageTemplateRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class MyAIImageTemplateService
{
    public function __construct(
        private StorageService $storageService,
        private MyAIImageTemplateRepository $myAIImageTemplateRepository
    ) {}

    public function getAll()
    {
        return $this->myAIImageTemplateRepository->getModel()->all()->toArray();
    }

    public function insert(array $images, int $imageTemplateCategoryId)
    {
        $insertedData = [];
        foreach ($images as $image) {
            $s3 = $this->storageService->putToS3($image, 'my_ai_image_collection');
            $data = [
                'my_ai_image_template_category_id' => $imageTemplateCategoryId,
                's3_url' => $s3['path'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $insertedData[] = $data;
        }
        return $this->myAIImageTemplateRepository->insert($insertedData);
    }

    public function destroy(int $id): bool
    {
        try {
            DB::beginTransaction();

            $imageTemplate = $this->myAIImageTemplateRepository->find($id);
            if (!$imageTemplate) {
                throw new DomainException('Không tìm thấy dữ liệu', Response::HTTP_NOT_FOUND);
            }

            $this->storageService->deleteFromS3($imageTemplate->s3_url);

            DB::commit();
            return  $imageTemplate->delete();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
