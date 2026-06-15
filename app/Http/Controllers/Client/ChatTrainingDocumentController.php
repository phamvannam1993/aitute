<?php

namespace App\Http\Controllers\Client;

use App\Helper\Helpers;
use App\Http\Controllers\Controller;
use App\Services\ChatTrainingDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ChatTrainingDocumentController extends Controller
{
    public function __construct(
        protected ChatTrainingDocumentService $chatTrainingDocumentService,
    ) {}

    public function getDocumentById(Request $request)
    {
        $document = $this->chatTrainingDocumentService->getDocumentById($request->id);
        return response()->json([
            'document' => $document,
        ]);
    }

    public function settingAgent(Request $request)
    {
        $user = Auth::user();
        $currentPage = 1;
        return Inertia::render('Client/ChatTrainingDocument/Index', [
            'toneConfigs' => config('configs.ai_tu_te_chat_tone'),
            'fanpage_dify_apps' => config('configs.fanpage_dify_apps'),
            'user' => $user,
            'documents' => [],
            'totalPage' => 0,
            'currentPage' => $currentPage
        ]);
    }

    public function uploadDocument(Request $request)
    {
        try {
            $request->validate([
                'documents' => 'required|array',
                'documents.*' => 'required|file|mimes:pdf,txt,csv,xls,xlsx|max:15360',
            ]);

            $documents = $request->file('documents');
            foreach ($documents as $document) {
                // $ext  = $document->getClientOriginalExtension();
                $localName = $document->getClientOriginalName();
                $localName = str_replace(' ', '_', $localName);
                $documentPath = 'documents/'. uniqid() . '_' . $localName;
                Storage::disk(config('constant.STORAGE_DISK'))->put($documentPath, file_get_contents($document));
                $this->chatTrainingDocumentService->createDocument([
                    'name' => $localName,
                    'content' => '',
                    'path' => $documentPath,
                    'user_id' => Auth::user()->id,
                    'project_id' => data_get($request, 'project_id'),
                    'fanpage_dify_app' => $request->get('fanpage_dify_app')
                ], $document);
            }
            return response()->json([
                'message' => 'Tài liệu đã được tải lên thành công.',
            ]);
        } catch (\Exception $e) {
            Helpers::logException($e);
            return response()->json([
                'message' => 'Tải lên tài liệu thất bại: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function chatTrainingDocuments(Request $request)
    {
        if (data_get($request, 'project_id')) {
            $listDocuments = $this->chatTrainingDocumentService->getListDocumentsPaginate($request->all());
        } else {
            $listDocuments = $this->chatTrainingDocumentService->getListDocumentsWithoutProjectPaginate($request->all());
        }
        return response()->json([
            'documents' => $listDocuments
        ]);
    }

    public function deleteDocument(Request $request)
    {
        $user = Auth::user();
        $document = $this->chatTrainingDocumentService->getDocumentById($request->id, $user->id);
        if ($document) {
            $this->chatTrainingDocumentService->deleteDocument($request->id, $user->id);
            return response()->json([
                'message' => 'Tài liệu đã được xóa thành công.',
            ]);
        }
        return response()->json([
            'message' => 'Tài liệu không tồn tại.',
        ], 404);
    }

    public function saveChatConfig(Request $request)
    {
        $request->validate([
            'tone_config' => 'required|string|in:'.implode(',', array_keys(config('configs.ai_tu_te_chat_tone'))),
        ]);

        $user = Auth::user();
        $user->tone_config = $request->tone_config;
        $user->save();
        return response()->json([
            'message' => 'Cấu hình đã được lưu thành công.',
        ]);
    }

    public function downloadDocument(Request $request)
    {
        $user = Auth::user();
        $document = $this->chatTrainingDocumentService->getDocumentById($request->id, $user->id);
        if ($document) {
            return Storage::disk(config('constant.STORAGE_DISK'))->download($document->path, $document->name);
        }
        return response()->json([
            'message' => 'Tài liệu không tồn tại.',
        ], 404);
    }
}