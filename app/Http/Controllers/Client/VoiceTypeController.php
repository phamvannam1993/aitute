<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\VoiceTypeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoiceTypeController extends Controller
{
    private $voiceTypeRepository;

    public function __construct(VoiceTypeRepository $voiceTypeRepository)
    {
        $this->voiceTypeRepository = $voiceTypeRepository;
    }

    public function getUserVoiceTypes()
    {
        try {
            $userId = Auth::id();
            $voiceTypes = $this->voiceTypeRepository->getVoiceTypesByUserId($userId);

            return response()->json([
                'success' => true,
                'data' => $voiceTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
