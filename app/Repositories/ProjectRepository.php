<?php

namespace App\Repositories;

use App\Models\Project;

class ProjectRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return Project::class;
    }

    public function getListProjectsByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function deleteProject($id, $userId)
    {
        return $this->model->where('id', $id)->where('user_id', $userId)->delete();
    }

    public function updateProject($id, $data, $userId)
    {
        return $this->model->where('id', $id)->where('user_id', $userId)->update($data);
    }

    public function getProjectById($id, $userId)
    {
        return $this->model->where('id', $id)->where('user_id', $userId)->first();
    }

    public function getListProjectsByUserIdPaginate($userId, $perPage)
    {
        return $this->model->where('user_id', $userId)->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function storeProject($data)
    {
        return $this->model->create($data);
    }
}
