<?php

namespace App\Repositories;

use App\Models\VoiceType;
use App\Models\User;

class VoiceTypeRepository extends AbstractRepository
{
    public function getModelClass()
    {
        return VoiceType::class;
    }

    public function getListVoice($userId) {
        $listVoice = VoiceType::select('id', 'name', 'type', 'demo', 'user_id', 'model')
            ->where(function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->orWhereNull('model')
                      ->orWhereNull('user_id');
            })
            ->orderBy("created_at", 'desc')
            ->get();
        return $listVoice;
    }

    public function getListVoiceClones() {
        $listVoice = VoiceType::select('id' ,'name', 'type','demo', 'model')
        -> where('model' , 'cloned')
        -> orderBy("created_at" , 'desc')
        ->get();
        return $listVoice;
    }

    public function getVoiceTypesByUserId($userId) {
        return VoiceType::select('id', 'name', 'type', 'demo', 'user_id' , 'model')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function deleteVoiceType($id) {
        return VoiceType::where('id', $id)->delete();
    }

    public function getAuthorCloneVoice($id){
        $clone_voice = User::where('id', $id)->first()->clone_voice;
        return $clone_voice;
    }
    public function getVoiceTypeByType($type) {
        return VoiceType::where('type', $type)->first();
    }
}
