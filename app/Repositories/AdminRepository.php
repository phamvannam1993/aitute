<?php

namespace App\Repositories;

use App\Enums\AdminRoleEnum;
use App\Models\Admin;

class AdminRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return Admin::class;
    }

    public function searchTeachers($limit = 10, array $params)
    {
        $search = $params['search'] ?? '';
        $appends = [
            'search' => $search,
        ];

        $list =  $this->getModel()
            ->role(AdminRoleEnum::teacher->value);

        if (isset($params['school_id'])) {
            $list =  $list->where('school_id', $params['school_id']);
        }

        if ($search) {
            $list =  $list->where(function ($query) use ($search) {
                $query->like('email', $search)
                    ->orLike('id', $search);
            });
        }

        $list =  $list
            ->orderBy('id', 'desc')
            ->paginate($limit)
            ->appends($appends);

        return $list;
    }

    public function findWithClasses($admin_id)
    {
        return $this->getModel()
            ->with(['classes', 'classes.users'])->findOrFail($admin_id);
    }

    // public function getUserWithCountSocials(string $social_id, string $social_type, string $email)
    // {
    //     return $this->withCount(
    //         [
    //             'userSocials' => function ($q) use ($social_id, $social_type) {
    //                 $q->where([
    //                     ['social_id', $social_id],
    //                     ['social_type', $social_type],
    //                 ]);
    //             }
    //         ]
    //     )->findByFilter([
    //         ['email', $email]
    //     ]);
    // }

    public function getUserWithSocials(string $social_id, string $social_type, string $email)
    {
        return $this->findByFilter([
            ['email', $email]
        ]);
    }


    public function findUserByEmail(string $value): ?Admin
    {
        return $this->model->where('email', $value)->first();
    }
}
