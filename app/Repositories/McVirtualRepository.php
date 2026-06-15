<?php

namespace App\Repositories;

use App\Models\AIGeneratedMedia;
use App\Models\Interest;
use App\Models\McVirtual;
use App\Models\Occupation;
use App\Models\Operation;
use Illuminate\Support\Facades\DB;

class McVirtualRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return McVirtual::class;
    }

    public function getHistories($userId, $per_page) {
        return McVirtual::query()
                ->where('user_id', '=', $userId)
                ->where('status', "done")
                ->select('id', 'mc_virtual_id', 'status', 'result_url', 'ai_generated_media_id', 'type', 'avatar_url')
                ->orderBy('created_at', direction: 'desc')
                ->paginate($per_page);
    }
    public function getHistoriesV2($userId, $per_page) {
        return McVirtual::query()
                ->where('user_id', '=', $userId)
                ->where('status', '!=', 'error')
                ->where('status', '!=', 'fail')
                ->select('id', 'mc_virtual_id', 'status', 'result_url', 'ai_generated_media_id', 'type', 'avatar_url')
                ->orderBy('created_at', direction: 'desc')
                ->paginate($per_page);
    }

    public function getAllNotDone($userId) {
        return McVirtual::query()
                ->where('user_id', '=', $userId)
                ->where('status', "!=", "done")
                ->where('status', "!=", "fail")
                ->where('status', "!=", "error")
                ->select('id', 'status', 'created_at')
                ->orderBy('created_at', direction: 'desc')
                ->get();
    }

    public function updateByMcVirtualId($id, $data) {
        $mcVirtual = McVirtual::where('mc_virtual_id', $id)->first();

        if (!$mcVirtual) {
            return false;
        }

        $mcVirtual->status = $data['status'] ?? $mcVirtual->status;
        $mcVirtual->result_url = $data['result_url'] ?? $mcVirtual->result_url;
        $mcVirtual->avatar_url = $data['avatar_url'] ?? $mcVirtual->avatar_url;

        if ($mcVirtual->save()) {
            return true;
        }

        return false;
    }

    public function getVideo($id) {
        return McVirtual::where('id', $id)->select('result_url')->first();
    }
    public function getAudio($id) {
        return AIGeneratedMedia::where('id', $id)->select('s3_url')->first();
    }
    public function findOne($id) {
        return McVirtual::where('id', $id)->first();
    }
    public function updateBy($id, $data = []) {
        return McVirtual::where('id', $id)->update($data);
    }
    public function getVideoProcessing($userId) {
        return McVirtual::query()
                ->where('user_id', '=', $userId)
                ->where('status', '!=', 'done')
                ->where('status', '!=', 'fail')
                ->where('status', '!=', 'error')
                ->select('*')
                ->first();

    }
}

