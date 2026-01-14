<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | The default AI provider to use for the assistant.
    | Supported: 'groq', 'openai', 'anthropic'
    |
    */
    'default' => env('AI_PROVIDER', 'groq'),

    /*
    |--------------------------------------------------------------------------
    | AI Providers
    |--------------------------------------------------------------------------
    |
    | Configuration for each AI provider. Prism will use these settings.
    |
    */
    'providers' => [
        'groq' => [
            'api_key' => env('GROQ_API_KEY'),
            'model' => env('AI_MODEL', 'llama-3.3-70b-versatile'),
        ],

        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('AI_MODEL', 'gpt-4'),
        ],

        'anthropic' => [
            'api_key' => env('ANTHROPIC_API_KEY'),
            'model' => env('AI_MODEL', 'claude-3-5-sonnet-latest'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Generation Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for AI text generation
    |
    */
    'max_tokens' => env('AI_MAX_TOKENS', 1000),

    'temperature' => env('AI_TEMPERATURE', 0.7),

    'timeout' => env('AI_TIMEOUT', 30),
];
