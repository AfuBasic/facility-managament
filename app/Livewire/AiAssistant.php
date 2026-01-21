<?php

namespace App\Livewire;

use App\Jobs\ProcessAiQuery;
use App\Services\AiAssistantService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Session;
use Livewire\Component;

class AiAssistant extends Component
{
    #[Session]
    public bool $isOpen = false;

    public string $message = '';

    #[Session]
    public array $conversation = [];

    public bool $isProcessing = false;

    protected AiAssistantService $aiService;

    public function boot(AiAssistantService $aiService): void
    {
        $this->aiService = $aiService;
    }

    public function mount(): void
    {
        // Only initialize with welcome message if conversation is empty
        if (empty($this->conversation)) {
            $this->conversation = [
                [
                    'role' => 'assistant',
                    'content' => 'Hi! I\'m your AI assistant. Ask me anything about your work orders, assets, facilities, stores, events, contacts, spaces, or users!',
                    'timestamp' => now()->toIso8601String(),
                ],
            ];
        }
    }

    public function toggleChat(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function sendMessage(): void
    {
        if (empty(trim($this->message))) {
            return;
        }

        // Add user message to conversation immediately
        $userMessage = trim($this->message);
        $this->conversation[] = [
            'role' => 'user',
            'content' => $userMessage,
            'timestamp' => now()->toIso8601String(),
        ];

        // Clear input
        $this->message = '';

        // Set processing state to show typing indicator
        $this->isProcessing = true;

        // Dispatch job to process AI query asynchronously
        ProcessAiQuery::dispatch(
            question: $userMessage,
            userId: (string) Auth::id(),
            componentId: $this->getId()
        );
    }

    public function getListeners(): array
    {
        $userId = Auth::id();

        return [
            "echo-private:user.{$userId},.ai.response.ready" => 'handleAiResponse',
        ];
    }

    public function handleAiResponse($event): void
    {
        // Only handle if this is for our component
        if ($event['componentId'] !== $this->getId()) {
            return;
        }

        // Add AI response to conversation
        $response = $event['response'];
        $this->conversation[] = [
            'role' => 'assistant',
            'content' => $response['answer'] ?? 'Sorry, I couldn\'t process that.',
            'links' => $response['links'] ?? [],
            'timestamp' => now()->toIso8601String(),
        ];

        // Reset processing state
        $this->isProcessing = false;
    }

    public function clearConversation(): void
    {
        $this->conversation = [
            [
                'role' => 'assistant',
                'content' => 'Conversation cleared. How can I help you?',
                'timestamp' => now()->toIso8601String(),
            ],
        ];
    }

    public function render()
    {
        return view('livewire.ai-assistant');
    }
}
