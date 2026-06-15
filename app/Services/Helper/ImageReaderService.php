<?php

namespace App\Services\Helper;

use App\Exceptions\CannotReadImageContentException;
use OpenAI;

class ImageReaderService
{
    /**
     * @throws CannotReadImageContentException
     */
    public function extractText(string $imagePath): ?string
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
            throw new CannotReadImageContentException();
        }

        return $content;
    }
}
