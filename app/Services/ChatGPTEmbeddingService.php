<?php

namespace App\Services;

use App\Exceptions\DomainException;
use Illuminate\Http\Response;
use App\Helper\Helpers;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use OpenAI;

class ChatGPTEmbeddingService
{
    protected $openai;

    public function __construct()
    {
        $this->openai = OpenAI::client(config('openai.api_key'));
    }

    private function createEmbedding($text)
    {
        $response = $this->openai->embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $text
        ]);
        return $response->embeddings[0]->embedding;
    }

    private function cosineSimilarity($a, $b)
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < count($a); $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    private function splitIntoChunks($text, $chunkSize = 1000)
    {
        // Chia theo từng đoạn để giữ nguyên ngữ nghĩa
        $paragraphs = explode("\n\n", $text);
        $chunks = [];
        $currentChunk = '';

        foreach ($paragraphs as $paragraph) {
            // Nếu đoạn hiện tại + đoạn mới > chunkSize
            if (strlen($currentChunk) + strlen($paragraph) > $chunkSize) {
                if (!empty($currentChunk)) {
                    $chunks[] = trim($currentChunk);
                }
                $currentChunk = $paragraph;
            } else {
                $currentChunk .= "\n\n" . $paragraph;
            }
        }

        if (!empty($currentChunk)) {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }

     // 3 ket qua
    public function search2($searchText, $document)
    {
        // 1. Chia document thành chunks
        $chunks = $this->splitIntoChunks($document, 8000);

        // 2. Tạo embedding cho search text
        $searchEmbedding = $this->createEmbedding($searchText);

        $results = [];
        // 3. Xử lý từng chunk
        foreach ($chunks as $index => $chunk) {
            $chunkEmbedding = $this->createEmbedding($chunk);
            $similarity = $this->cosineSimilarity($searchEmbedding, $chunkEmbedding);

            $results[] = [
                'chunk_index' => $index,
                'text' => $chunk,
                'score' => $similarity
            ];
        }

        // 4. Sắp xếp kết quả
        usort($results, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice($results, 0, 3); // trả về top 3
    }

    // 1 ket qua
    public function search($searchText, $document)
    {
        $chunks = $this->splitIntoChunks($document, 8000);
        $searchEmbedding = $this->createEmbedding($searchText);
        $bestMatch = null;
        $highestScore = -1;
        foreach ($chunks as $index => $chunk) {
            $chunkEmbedding = $this->createEmbedding($chunk);
            $similarity = $this->cosineSimilarity($searchEmbedding, $chunkEmbedding);
            if ($similarity > $highestScore) {
                $highestScore = $similarity;
                $bestMatch = [
                    'chunk_index' => $index,
                    'text' => $chunk,
                    'score' => $similarity
                ];
            }
        }
        return [$bestMatch];
    }

}
