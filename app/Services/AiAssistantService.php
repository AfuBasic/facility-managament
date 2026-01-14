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
    public function ask(string $question): array
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

            // Parse and format the response
            return $this->responseFormatter->format($aiResponse, $question);
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
You are an AI assistant for a facility management system. Help users query their data using natural language.

DATABASE SCHEMA:
{$schemaContext}

USER QUESTION:
{$question}

INSTRUCTIONS:
1. Analyze the question and identify the relevant model (Asset, WorkOrder, Facility, or Store)
2. Identify any relationships or filters needed
3. Generate a JSON response with this EXACT structure:

{
    "intent": "Brief description of what the user wants",
    "model": "MUST be one of: Asset, WorkOrder, Facility, or Store",
    "query_instructions": {
        "where": [{"field": "field_name", "operator": "=", "value": "value"}],
        "whereHas": [{"relation": "facility", "field": "name", "operator": "like", "value": "%Facility A%"}],
        "orderBy": {"field": "created_at", "direction": "desc"},
        "limit": 100,
        "with": ["facility", "store"]
    },
    "response_template": "You have {count} [items]"
}

EXAMPLES:

Question: "How many assets do we have?"
Response:
{
    "intent": "Count total assets",
    "model": "Asset",
    "query_instructions": {
        "where": [],
        "whereHas": [],
        "orderBy": {"field": "created_at", "direction": "desc"},
        "limit": 100,
        "with": []
    },
    "response_template": "You have {count} assets"
}

Question: "Show me open work orders"
Response:
{
    "intent": "List open work orders",
    "model": "WorkOrder",
    "query_instructions": {
        "where": [{"field": "status", "operator": "=", "value": "open"}],
        "whereHas": [],
        "orderBy": {"field": "created_at", "direction": "desc"},
        "limit": 10,
        "with": ["facility"]
    },
    "response_template": "You have {count} open work orders"
}

Question: "How many stores are in Facility A?"
Response:
{
    "intent": "Count stores in Facility A",
    "model": "Store",
    "query_instructions": {
        "where": [],
        "whereHas": [{"relation": "facility", "field": "name", "operator": "like", "value": "%Facility A%"}],
        "orderBy": {"field": "name", "direction": "asc"},
        "limit": 100,
        "with": ["facility"]
    },
    "response_template": "Facility A has {count} stores"
}

Question: "Show me assets at Main Building"
Response:
{
    "intent": "List assets at Main Building facility",
    "model": "Asset",
    "query_instructions": {
        "where": [],
        "whereHas": [{"relation": "facility", "field": "name", "operator": "like", "value": "%Main Building%"}],
        "orderBy": {"field": "name", "direction": "asc"},
        "limit": 20,
        "with": ["facility"]
    },
    "response_template": "Found {count} assets at Main Building"
}

CRITICAL RULES:
- ALWAYS provide a "model" value (Asset, WorkOrder, Facility, or Store)
- Use "whereHas" for filtering by related models (e.g., stores in a facility)
- Use "where" for filtering the main model's own fields
- Use "with" to eager load relationships for better performance
- Use LIKE operator with % wildcards for partial name matches
- Keep queries simple and safe (read-only)
- Respond ONLY with valid JSON, no additional text

Generate the JSON response now:
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
