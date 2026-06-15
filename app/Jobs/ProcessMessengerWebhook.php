<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\MessengerWebhookService;


class ProcessMessengerWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $messaging;

    public function __construct(array $messaging)
    {
        $this->messaging = $messaging;
    }

    public function handle(MessengerWebhookService $MessengerWebhookService)
    {
        return $MessengerWebhookService->handle($this->messaging);
    }
}