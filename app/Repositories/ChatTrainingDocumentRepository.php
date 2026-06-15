<?php

namespace App\Repositories;

use App\Models\ChatTrainingDocument;
use Illuminate\Support\Facades\Auth;

class ChatTrainingDocumentRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return ChatTrainingDocument::class;
    }

    public function getListDocumentsByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function getListDocumentsByProjectId($projectId, $userId)
    {
        return $this->model->where('project_id', $projectId)->where('user_id', $userId)->get();
    }

    public function getListDocumentsPaginate($userId, $params)
    {
        $query = $this->model->where('user_id', $userId);
        $query = $this->_buildGetListDocumentsQuery($query, $params);
        return $query->orderBy('created_at', 'desc')->paginate(data_get($params, 'per_page', 10));
    }

    public function getListDocumentsWithoutProjectPaginate($userId, $params)
    {
        $query = $this->model->where('user_id', $userId)->whereNull('project_id');
        $query = $this->_buildGetListDocumentsQuery($query, $params);
        return $query->orderBy('created_at', 'desc')->paginate(data_get($params, 'per_page', 10));
    }

    private function _buildGetListDocumentsQuery($query, $params)
    {
        if (isset($params['page'])) {
            $perPage = data_get($params, 'per_page', 10);
            $query->offset($params['page'] * $perPage);
            $query->limit($perPage);
        }

        if (isset($params['project_id'])) {
            $query->where('project_id', $params['project_id']);
        }

        if (isset($params['search'])) {
            $query->where('name', 'like', '%' . $params['search'] . '%');
        }

        return $query;
    }

    public function getDocumentById($id, $userId)
    {
        return $this->model->where('id', $id)->where('user_id', $userId)->first();
    }

    public function deleteDocument($id, $userId)
    {
        return $this->model->where('id', $id)->where('user_id', $userId)->delete();
    }

    public function createDocument($data)
    {
        return $this->model->create($data);
    }

    public function updateDocument($id, $data, $userId)
    {
        return $this->model->where('id', $id)->where('user_id', $userId)->update($data);
    }
}
