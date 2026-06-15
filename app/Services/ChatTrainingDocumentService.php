<?php

namespace App\Services;

use App\Repositories\ChatTrainingDocumentRepository;
use App\Repositories\ProjectRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatTrainingDocumentService
{
    public function __construct(
        private ChatTrainingDocumentRepository $chatTrainingDocumentRepository,
        private ProjectRepository $projectRepository
    ) {}

    public function getListDocumentsPaginate($params)
    {
        return $this->chatTrainingDocumentRepository->getListDocumentsPaginate(Auth::user()->id, $params);
    }

    public function getListDocumentsWithoutProjectPaginate($params)
    {
        return $this->chatTrainingDocumentRepository->getListDocumentsWithoutProjectPaginate(Auth::user()->id, $params);
    }

    public function deleteDocument($id)
    {
        $document = $this->chatTrainingDocumentRepository->getDocumentById($id, Auth::user()->id);
        if (empty($document)) {
            throw new \Exception('Dữ liệu không tồn tại!');
        }
        $project = $this->projectRepository->getProjectById(data_get($document, 'project_id'), Auth::user()->id);
        if ($project) {
            $projectDatasetId = data_get($project, 'dify_dataset_id');
        }else {
            $projectDatasetId = Auth::user()->getDifyDatasetId();
        }
        $this->_deleteDifyDocument($projectDatasetId, data_get($document, 'dify_document_id'));
        return $this->chatTrainingDocumentRepository->deleteDocument($id, Auth::user()->id);
    }

    public function createDocument($data, $document)
    {
        if (!empty($data['project_id'])) {
            $projectDatasetId = data_get($this->projectRepository->getProjectById($data['project_id'], Auth::user()->id), 'dify_dataset_id');
        } else {
            $projectDatasetId = Auth::user()->getDifyDatasetId();
        }

        if (empty($projectDatasetId)) {
            Log::error('Không tìm thấy project: ' . (string) $projectDatasetId);
            throw new \Exception('Tạo dữ liệu thất bại!');
        }

        $difyDocumentId = $this->_createDifyDocument($projectDatasetId, $document);

        if (!$difyDocumentId) {
            Log::error('Không tạo được document: ' . (string) $difyDocumentId);
            throw new \Exception('Tạo dữ liệu thất bại!');
        }
        $data['dify_document_id'] = $difyDocumentId;
        return $this->chatTrainingDocumentRepository->createDocument($data);
    }

    public function updateDocument($id, $data)
    {
        return $this->chatTrainingDocumentRepository->updateDocument($id, $data, Auth::user()->id);
    }

    public function getDocumentById($id)
    {
        return $this->chatTrainingDocumentRepository->getDocumentById($id, Auth::user()->id);
    }

    private function _createDifyDocument($projectDatasetId, $document)
    {
        try {
            $apiAccessKey = config('services.dify.api_access_key');
            $baseUrl = config('services.dify.base_url');

            $client = new Client();

            $response = $client->post($baseUrl . '/v1/datasets/' . $projectDatasetId . '/document/create-by-file', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiAccessKey,
                ],
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => fopen($document->getRealPath(), 'r'),
                        'filename' => $document->getClientOriginalName(),
                        'headers'  => [
                            'Content-Type' => $document->getMimeType()
                        ]
                    ],
                    [
                        'name'     => 'data',
                        'contents' => json_encode([
                            "indexing_technique" => "high_quality",
                            "process_rule" => [
                                "rules" => [
                                    "pre_processing_rules" => [
                                        ["id" => "remove_extra_spaces", "enabled" => false],
                                        ["id" => "remove_urls_emails", "enabled" => false],
                                    ],
                                    "segmentation" => [
                                        "separator" => "###",
                                        "max_tokens" => 4000
                                    ]
                                ],
                                "mode" => "custom"
                            ]
                        ]),
                        'headers' => [
                            'Content-Type' => 'text/plain'
                        ]
                    ],
                    [
                        'name'     => 'name',
                        'contents' => $document->getClientOriginalName(),
                    ]
                ],
            ]);
            return data_get(json_decode($response->getBody(), true), 'document.id');
        } catch (\Exception $e) {
            Log::error('Tạo dữ liệu thất bại: ' . $e->getMessage());
            return null;
        }
    }

    private function _deleteDifyDocument($projectDatasetId, $difyDocumentId)
    {
        try {
            $apiAccessKey = config('services.dify.api_access_key');
            $baseUrl = config('services.dify.base_url');

            $client = new Client();

            $response = $client->delete($baseUrl . "/v1/datasets/{$projectDatasetId}/documents/{$difyDocumentId}", [
                'headers' => [
                    'Authorization' => "Bearer {$apiAccessKey}",
                ]
            ]);

            if ($response->getStatusCode() === 204) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Xóa dữ liệu thất bại: ' . $e->getMessage());
            return false;
        }
    }
}
