<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DeepSeek API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for DeepSeek AI integration in Eli Boutique.
    |
    */

    'api_key' => env('DEEPSEEK_API_KEY'),
    'base_url' => env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com'),
    'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
    'max_tokens' => (int) env('DEEPSEEK_MAX_TOKENS', 2000),
    'temperature' => (float) env('DEEPSEEK_TEMPERATURE', 0.2),
    'timeout' => (int) env('DEEPSEEK_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | Guardrails & Safety
    |--------------------------------------------------------------------------
    |
    | Max rows to return per SQL tool query, preventing memory exhaustion.
    |
    */
    'sql_max_rows' => 50,
];
