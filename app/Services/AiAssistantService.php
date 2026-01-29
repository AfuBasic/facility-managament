<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Prism\Prism\Prism;

class AiAssistantService
{
    protected string $provider;

    protected string $model;

    protected int $maxTokens;

    protected float $temperature;

    protected int $timeout;

    public function __construct(
        protected DatabaseSchemaProvider $schemaProvider,
        protected ResponseFormatter $responseFormatter
    ) {
        $this->provider = config('ai.default') ?? 'anthropic';
        $this->model = config("ai.providers.{$this->provider}.model") ?? 'claude-3-5-sonnet-latest';
        $this->maxTokens = config('ai.max_tokens') ?? 1000;
        $this->temperature = config('ai.temperature') ?? 0.7;
        $this->timeout = config('ai.timeout') ?? 30;
    }

    /**
     * Ask the AI assistant a question
     */
    public function ask(string $question, ?int $clientAccountId = null): array
    {
        try {
            // Get database schema context
            $schemaContext = $this->schemaProvider->getSchemaContext();

            // Build the prompt
            $prompt = $this->buildPrompt($question, $schemaContext);

            // Call AI using Prism
            $aiResponse = $this->callAi($prompt);

            // Log the raw AI response for debugging
            Log::info('AI Raw Response', [
                'question' => $question,
                'response' => $aiResponse,
            ]);

            // Parse and format the response with client scoping
            return $this->responseFormatter->format($aiResponse, $question, $clientAccountId);
        } catch (\Exception $e) {
            Log::error('AI Assistant Error', [
                'question' => $question,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'answer' => 'Sorry, I encountered an error processing your question. Please try again.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build the prompt for the AI
     */
    protected function buildPrompt(string $question, string $schemaContext): string
    {
        return <<<PROMPT
You are an AI assistant for a facility management system. Generate JSON to query data.

SCHEMA:
{$schemaContext}

QUESTION: {$question}

RESPOND WITH THIS JSON STRUCTURE ONLY:
{
    "intent": "Brief description",
    "model": "Asset|WorkOrder|Facility|Store|Event|Contact|Space|User",
    "query_instructions": {
        "where": [{"field": "field_name", "operator": "=", "value": "value"}],
        "whereHas": [{"relation": "facility", "field": "name", "operator": "like", "value": "%Name%"}],
        "orderBy": {"field": "created_at", "direction": "desc"},
        "limit": 10,
        "with": ["facility"]
    },
    "response_template": "You have {count} [items]"
}

EXAMPLES:

Q: "How many open work orders?"
{"intent":"Count open work orders","model":"WorkOrder","query_instructions":{"where":[{"field":"status","operator":"=","value":"open"}],"whereHas":[],"orderBy":{"field":"created_at","direction":"desc"},"limit":100,"with":[]},"response_template":"You have {count} open work orders"}

Q: "Show me stores in Facility A"
{"intent":"List stores in Facility A","model":"Store","query_instructions":{"where":[],"whereHas":[{"relation":"facility","field":"name","operator":"like","value":"%Facility A%"}],"orderBy":{"field":"name","direction":"asc"},"limit":20,"with":["facility"]},"response_template":"Facility A has {count} stores"}

Q: "Upcoming events"
{"intent":"List upcoming events","model":"Event","query_instructions":{"where":[{"field":"starts_at","operator":">=","value":"now"}],"whereHas":[],"orderBy":{"field":"starts_at","direction":"asc"},"limit":10,"with":[]},"response_template":"You have {count} upcoming events"}

RULES:
- ALWAYS include "model" (required)
- Use "whereHas" for filtering by related models
- Use LIKE with % for partial name matches
- Respond with JSON only, no extra text
PROMPT;
    }

    /**
     * Call the AI using Prism
     */
    protected function callAi(string $prompt): string
    {
        $prism = new Prism;
        $response = $prism->text()
            ->using($this->provider, $this->model)
            ->withSystemPrompt('You are a helpful assistant that generates JSON responses for database queries.')
            ->withPrompt($prompt)
            ->withMaxTokens($this->maxTokens)
            ->withClientOptions([
                'temperature' => $this->temperature,
            ])
            ->generate();

        return $response->text;
    }
}
