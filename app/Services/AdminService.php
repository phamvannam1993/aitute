<?php

namespace App\Services;

use App\Helper\ApiErrorsMessageBag;
use App\Models\Admin;
use App\Repositories\AdminRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class AdminService
{
    public function __construct(
        protected AdminRepository $adminRepository,
    ) {
    }

    public function getTeachersByManager(array $params)
    {
        $admin = auth('admin')->user();

        $params = array_merge($params, [
            'school_id' => $admin->school_id
        ]);
        $teachers = $this->adminRepository->searchTeachers(limit: 10, params: $params);
        $teachers = $this->setRatioTeacherSlide($teachers);

        return $teachers;
    }

    private function setRatioTeacherSlide($teachers)
    {
        $admins = $teachers->getCollection();
        if ($admins) {
            $admins = $this->ratioTeacherSlide($admins);
            $teachers = $teachers->setCollection($admins);
        }

        return $teachers;
    }
    private function ratioTeacherSlide($admins)
    {
        $totalNumberOfLessonRequired = config('constants.school.total_number_of_lesson_required');

        foreach ($admins as $key => $admin) {
            $admin->ratio_teacher_slide = round($admin->semesterMaterials->count() / $totalNumberOfLessonRequired, 2) * 100;
            $admin->total_slide = $admin->semesterMaterials->count();
            $admin->total_student = 0;
            foreach ($admin->classes as $key => $class) {
                $admin->total_student += $class->users->count();
            }
        }

        return $admins;
    }

    public function store($data, bool $loginAfterRegister = true): array
    {
        try {
            $email = $data->get('email');
            $password = $data->get('password');

            $data = [
                'email' => $email,
                'password' => Hash::make($password),
                'social_type' => 'google'
            ];

            $admin = $this->adminRepository->updateOrCreate(['email' => $email], $data);
            $token = $loginAfterRegister ? auth('admin')->login($admin, true) : '';

            return [
                'status' => true,
                'token' => $token,
                'admin' => $admin,
                'statusCode' => JsonResponse::HTTP_OK
            ];
        } catch (\Exception $exception) {
            // DB::rollBack();
            // report($exception);
            Log::error($exception->getMessage());

            return [
                'status' => false,
                'errors' => new ApiErrorsMessageBag(['form' => 'SERVER ERROR: Please contact the administrator']),
                'statusCode' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            ];
        }
    }
}
