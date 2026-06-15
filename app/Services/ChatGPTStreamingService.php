<?php

namespace App\Services;

use App\Exceptions\DomainException;
use Illuminate\Http\Response;
use App\Helper\Helpers;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Http\Request;

class ChatGPTStreamingService
{
  protected $client;
  public function __construct() {}

  public function stream(String $prompt, String $model = 'gpt-4o-mini')
  {
    $stream = OpenAI::chat()->createStreamed([
      'model' => $model,
      'messages' => [
        ['role' => 'user', 'content' => $prompt]
      ],
    ]);

    return $stream;
  }

  // ... các phần khác giữ nguyên ...

  public function streamMessage(String $prompt, String $model = 'gpt-4o')
  {
    try {
      $stream = OpenAI::chat()->createStreamed([
        'model' => $model,
        'messages' => [
          ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0.7,
        'max_tokens' => 2000,
        'stream' => true,
      ]);

      return $stream; // Trả về trực tiếp stream object
    } catch (\Exception $e) {
      Log::error('ChatGPT Streaming Error:', [
        'message' => $e->getMessage(),
        'prompt' => $prompt
      ]);
      throw $e;
    }
  }
}
