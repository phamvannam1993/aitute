<?php

return [
  'url' => env('OPENAI_URL', 'https://api.openai.com/v1/chat/completions'),
  'api_key' => env('OPENAI_API_KEY'),
  'image_url' => env('OPENAI_IMAGE_URL', 'https://api.openai.com/v1/images/generations'),
  'image_model' => env('OPENAI_IMAGE_MODEL', 'gpt-image-2'),
];
