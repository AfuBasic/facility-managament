<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ResponseFormatter
{
    protected QueryInterpreter $queryInterpreter;

    public function __construct(QueryInterpreter $queryInterpreter)
    {
        $this->queryInterpreter = $queryInterpreter;
    }

    /**
     * Format the AI response into a user-friendly message
     */
    public function format(string $aiResponse, string $originalQuestion): array
    {
        try {
            // Claude wraps JSON in markdown code blocks, so strip them
            $aiResponse = preg_replace('/^```json\s*/m', '', $aiResponse);
            $aiResponse = preg_replace('/\s*```$/m', '', $aiResponse);
            $aiResponse = trim($aiResponse);

            // Parse AI JSON response
            $parsed = json_decode($aiResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Parse Error', [
                    'error' => json_last_error_msg(),
                    'response' => $aiResponse,
                ]);
                throw new \Exception('Invalid JSON response from AI');
            }

            // Validate required fields
            if (! isset($parsed['model']) || empty($parsed['model'])) {
                return [
                    'success' => false,
                    'answer' => "I understand you're asking about: \"{$originalQuestion}\", but I need more specific information. Try asking about specific items like 'How many assets do we have?' or 'Show me open work orders'.",
                ];
            }

            // Execute the query
            $queryResult = $this->queryInterpreter->execute($parsed);

            if (! $queryResult['success']) {
                return [
                    'success' => false,
                    'answer' => "I had trouble processing that query. Could you rephrase your question? For example: 'How many work orders are open?' or 'Show me all assets'.",
                ];
            }

            // Build the response
            $answer = $this->buildAnswer($parsed, $queryResult);
            $links = $this->buildLinks($parsed, $queryResult);

            return [
                'success' => true,
                'answer' => $answer,
                'data' => $queryResult['data'],
                'count' => $queryResult['count'],
                'links' => $links,
                'intent' => $parsed['intent'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'answer' => "I couldn't process that question. Could you try rephrasing it? For example: 'How many assets do we have?' or 'Show me open work orders'.",
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build a natural language answer
     */
    protected function buildAnswer(array $parsed, array $queryResult): string
    {
        $template = $parsed['response_template'] ?? 'Found {count} results';
        $count = $queryResult['count'];
        $model = $parsed['model'] ?? '';

        // Replace {count} placeholder
        $answer = str_replace('{count}', $count, $template);

        // Handle pluralization intelligently
        if ($count === 1) {
            // Singularize common model names
            $singularMap = [
                'facilities' => 'facility',
                'assets' => 'asset',
                'work orders' => 'work order',
                'stores' => 'store',
                'results' => 'result',
            ];

            foreach ($singularMap as $plural => $singular) {
                $answer = str_ireplace($plural, $singular, $answer);
            }
        }

        // Add context based on count
        if ($count === 0) {
            $answer .= ' at this time.';
        }

        return $answer;
    }

    /**
     * Build relevant links based on the query
     */
    protected function buildLinks(array $parsed, array $queryResult): array
    {
        $links = [];
        $model = $parsed['model'] ?? null;

        if (! $model) {
            return $links;
        }

        // Map models to routes (all routes use /app/ prefix)
        $routeMap = [
            'WorkOrder' => '/app/work-orders',
            'Asset' => '/app/facilities', // Assets are viewed through facilities
            'Facility' => '/app/facilities',
            'Store' => '/app/facilities', // Stores are viewed through facilities
        ];

        if (isset($routeMap[$model])) {
            $route = $routeMap[$model];

            $links[] = [
                'text' => "View all {$model}s",
                'url' => $route,
            ];

            // Add filtered link if there are where clauses
            if (isset($parsed['query_instructions']['where']) && ! empty($parsed['query_instructions']['where'])) {
                $where = $parsed['query_instructions']['where'][0] ?? null;
                if ($where && isset($where['field'], $where['value'])) {
                    $links[] = [
                        'text' => 'View filtered results',
                        'url' => "{$route}?filter={$where['field']}:{$where['value']}",
                    ];
                }
            }
        }

        return $links;
    }
}
