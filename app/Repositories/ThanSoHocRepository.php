<?php

namespace App\Repositories;

use App\Models\ThanSoHoc;
use App\Models\ThanSoHocChatMessage;

class ThanSoHocRepository extends AbstractRepository
{
  public function getModelClass()
  {
    return ThanSoHoc::class;
  }

  public function findById($id)
  {
    return $this->model
      ->with(['chatMessages' => function ($query) {
        $query->orderBy('created_at', 'asc');
      }])
      ->findOrFail($id);
  }

  public function create($data)
  {
    return $this->model->create($data);
  }

  public function update($id, array $data)
  {
    $thanSoHoc = $this->model->findOrFail($id);
    $thanSoHoc->update($data);
    return $thanSoHoc;
  }
}
