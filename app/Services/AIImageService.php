<?php

namespace App\Services;

use App\Exceptions\DomainException;
use App\Helper\Helpers;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;

class AIImageService
{
    private $getImgApiBaseUrl;
    private $getImgApiModelPath;
    private $getImgToken;
    private $apiReplicateFluxSchnellUrl = 'https://api.replicate.com/v1/models/black-forest-labs/flux-schnell/predictions';
    private $replicateApiToken;

    public function __construct(
        private ChatGPTService $chatGPTService
    ) {
        $this->getImgApiBaseUrl = env('GETIMG_API_BASE_URL', 'https://api.getimg.ai/v1/');
        $this->getImgApiModelPath = env('GETIMG_API_MODEL_PATH');
        $this->getImgToken = env('GETIMG_API_TOKEN');
        $this->replicateApiToken = env('REPLICATE_API_TOKEN');
    }

    private function buildApiUrlGetImg()
    {
        $baseUrl = rtrim($this->getImgApiBaseUrl, '/');
        $modelPath = trim($this->getImgApiModelPath, '/');

        if (empty($modelPath)) {
            return $baseUrl;
        } else {
            return $baseUrl . '/' . $modelPath . '/text-to-image';
        }
    }
    /**
     * @param string $prompt
     * @throws \App\Exceptions\DomainException
     * @return array<int, array{url: string}> 
     */
    public function generateImageForPost(string $prompt): array
    {
        try {

            $apiUrl = $this->buildApiUrlgetImg();

            $requestData = [
                'prompt' => $prompt . ', hd',
                "output_format" => "png",
                "response_format" => "url",
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$this->getImgToken}",
            ])->post($apiUrl, $requestData);

            if ($response->failed()) {
                throw new DomainException('Không thể kết nối tới API. Vui lòng thử lại sau', Response::HTTP_BAD_REQUEST);
            }

            $responseData = $response->json();

            if (!isset($responseData['url'])) {
                throw new DomainException('Tạo ảnh không thành công. Vui lòng kiểm tra lại yêu cầu.', Response::HTTP_BAD_REQUEST);
            }

            return [
                [
                    'url' => $responseData['url']
                ]
            ];
        } catch (\Exception $e) {
            throw new DomainException('Lỗi khi tạo ảnh: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    public function generateImageReplicate($prompt, $aspect_ratio = '1:1')
    {
        $apiUrl = $this->apiReplicateFluxSchnellUrl;

        $requestData = [
            'input' => [
                'prompt' => $prompt,
                'go_fast' => true,
                'megapixels' => '1',
                'num_outputs' => 1,
                'aspect_ratio' => $aspect_ratio,
                'output_format' => 'png',
                'output_quality' => 100,
                'num_inference_steps' => 4
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->replicateApiToken}",
                'Content-Type' => 'application/json',
                'Prefer' => 'wait',
            ])->post($apiUrl, $requestData);

            if ($response->failed()) {
                throw new DomainException('Không thể kết nối tới API Replicate. Vui lòng thử lại sau.');
            }

            $responseData = $response->json();
            if (!isset($responseData['output'])) {
                throw new DomainException('Không nhận được phản hồi hợp lệ từ Replicate.');
            }

            return $responseData['output'][0];
        } catch (\Exception $e) {
            throw new DomainException('Error generating image from Replicate: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

}
