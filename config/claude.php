<?php

return [
  'url' => env('CLAUDE_URL', 'https://api.anthropic.com/v1/messages'),
  'api_key' => env('CLAUDE_API_KEY'),
  'chat_model' => env('CLAUDE_CHAT_MODEL', 'claude-2.1'),
  'version' => '2023-06-01'
];
