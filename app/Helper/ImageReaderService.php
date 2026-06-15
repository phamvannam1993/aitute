<?php

namespace App\Helper;

use Exception;
use Illuminate\Support\Facades\Log;
use OpenAI;
use Illuminate\Http\UploadedFile;

class ImageReaderService
{
    public function extractText(string $imagePath): ?string
    {
        $base64 = base64_encode(file_get_contents($imagePath));
        $client = OpenAI::client(config('openai.api_key'));
        $response = $client->chat()->create([
            'model' => 'gpt-4o',
            'temperature' => 1,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Mô tả nội dung của bức ảnh này bằng Tiếng Việt. Hãy mô tả nội dung của ảnh càng chi tiết càng tốt. Nếu nội dung ảnh không có gì, hãy trả về `nothing`',
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:image/jpeg;base64,$base64"
                            ]
                        ]
                    ],

                ],
            ],
        ]);

        $content = $response->choices[0]->message->content;

        if ($content === 'nothing') {
            throw new Exception();
        }

        return $content;
    }

    public function extractTextEnglish(string $imagePath): ?string
    {
        $base64 = base64_encode(file_get_contents($imagePath));
        $client = OpenAI::client(config('openai.api_key'));
        try {
            $response = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'temperature' => 1,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Mô tả nội dung của bức ảnh này bằng Tiếng Anh. Hãy mô tả nội dung của ảnh càng chi tiết càng tốt. Chỉ cần mô tả nội dung bỏ đi các cụm từ mở đầu. Nếu nội dung ảnh không có gì, hãy trả về `nothing`',
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:image/jpeg;base64,$base64"
                                ]
                            ]
                        ],

                    ],
                ],
            ]);

            $content = $response->choices[0]->message->content;

            if ($content === 'nothing') {
                throw new Exception();
            }

            return $content;
        } catch (Exception $e) {
            Log::error('OpenAI API failed: ' . $e->getMessage());
            return $this->extractTextUsingClaude3($base64);
        }
    }

    private function extractTextUsingClaude3(string $base64): ?string
    {
        try {
            $apiUrl = 'https://api.anthropic.com/v1/messages';

            $payload = [
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 1024,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'image',
                                'source' => [
                                    'type' => 'base64',
                                    'media_type' => 'image/jpeg',
                                    'data' => $base64,
                                ],
                            ],
                            [
                                'type' => 'text',
                                "text" => "Describe this image."
                            ],
                        ],
                    ],
                ],
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . config('claude.api_key'),
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                Log::error('Claude3 API request failed with HTTP code: ' . $httpCode);
                return null;
            }

            $responseData = json_decode($response, true);

            if (isset($responseData['message']['content'])) {
                return $responseData['message']['content'];
            }

            Log::error('Claude3 API response does not contain valid content.');
            return null;
        } catch (Exception $e) {
            Log::error('Error calling Claude3 API: ' . $e->getMessage());
            return null;
        }
    }
    public function promptWithImage(string $imagePath, $prompt): ?string
    {
        $base64 = base64_encode(file_get_contents($imagePath));
        $client = OpenAI::client(config('openai.api_key'));
        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'temperature' => 1,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt,
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:image/jpeg;base64,$base64"
                            ]
                        ]
                    ],

                ],
            ],
        ]);

        $content = $response->choices[0]->message->content;

        if ($content === 'nothing') {
            throw new Exception();
        }

        return $content;
    }

    public function promptWithFileImage(UploadedFile $imageFile, string $prompt): ?string
    {
        $base64 = base64_encode(file_get_contents($imageFile->getRealPath()));
        $mimeType = $imageFile->getClientMimeType();

        $client = OpenAI::client(config('openai.api_key'));

        $response = $client->chat()->create([
            'model' => 'gpt-4o',
            'temperature' => 1,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt,
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:$mimeType;base64,$base64"
                            ]
                        ]
                    ],
                ],
            ],
        ]);

        $content = $response->choices[0]->message->content;

        if ($content === 'nothing') {
            throw new Exception("Không có phản hồi từ AI.");
        }

        return $content;
    }

    public function promptWithFileImageV2($imageUrl, string $prompt): ?string
    {
        $base64 = base64_encode(file_get_contents($imageUrl));
        $client = OpenAI::client(config('openai.api_key'));

        $response = $client->chat()->create([
            'model' => 'gpt-4o',
            'temperature' => 1,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt,
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:image/png;base64,$base64"
                            ]
                        ]
                    ],
                ],
            ],
        ]);

        $content = $response->choices[0]->message->content;

        if ($content === 'nothing') {
            throw new Exception("Không có phản hồi từ AI.");
        }

        return $content;
    }
}
