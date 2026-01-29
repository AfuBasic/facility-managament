<?php

namespace App\Jobs;

use App\Events\AiResponseReady;
use App\Services\AiAssistantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAiQuery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $question,
        public string $userId,
        public string $componentId,
        public ?int $clientAccountId = null
    ) {}

    public function handle(AiAssistantService $aiService): void
    {
        // Get AI response with client scoping
        $response = $aiService->ask($this->question, $this->clientAccountId);

        // Broadcast event with response
        broadcast(new AiResponseReady(
            userId: $this->userId,
            componentId: $this->componentId,
            response: $response
        ));
    }
}
