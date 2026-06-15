<?php

namespace App\Services\DifyBackup;

class ChatGPT
{
  public function sendRequest($systemPrompt, $userPrompt): ?string
  {
    $client = \OpenAI::client(config('openai.api_key'));

    $response = $client->chat()->create([
      'model' => 'gpt-4o',
      'messages' => [
        [
          'role' => 'system',
          'content' => $systemPrompt
        ],
        [
          'role' => 'user',
          'content' => $userPrompt
        ]
      ],
      'temperature' => 0.7,
    ]);

    return $response->choices[0]->message->content;
  }
}
