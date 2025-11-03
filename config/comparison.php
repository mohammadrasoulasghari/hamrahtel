<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Maximum Rows Per File
    |--------------------------------------------------------------------------
    |
    | Maximum number of rows allowed per file for comparison.
    | Files exceeding this limit will show a warning and sampling option.
    |
    */

    'max_rows_per_file' => env('COMPARISON_MAX_ROWS', 5000),

    /*
    |--------------------------------------------------------------------------
    | Chunk Size
    |--------------------------------------------------------------------------
    |
    | Number of rows to process at a time when reading large files.
    | Smaller chunks use less memory but may take slightly longer.
    |
    */

    'chunk_size' => env('COMPARISON_CHUNK_SIZE', 500),

    /*
    |--------------------------------------------------------------------------
    | Enable AI Analysis
    |--------------------------------------------------------------------------
    |
    | Whether to enable AI-powered semantic analysis via OpenRouter.
    | When disabled, only PHP structural analysis will be performed.
    |
    */

    'enable_ai_analysis' => env('COMPARISON_ENABLE_AI', true),

    /*
    |--------------------------------------------------------------------------
    | AI Model
    |--------------------------------------------------------------------------
    |
    | The AI model to use for semantic analysis.
    | Default: openai/gpt-4o-mini (cost-effective, fast)
    |
    */

    'ai_model' => env('COMPARISON_AI_MODEL', 'openai/gpt-4o-mini'),

    /*
    |--------------------------------------------------------------------------
    | Processing Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum time in seconds to allow for processing.
    | Prevents hanging on very large files.
    |
    */

    'timeout' => env('COMPARISON_TIMEOUT', 120),

    /*
    |--------------------------------------------------------------------------
    | Maximum File Size
    |--------------------------------------------------------------------------
    |
    | Maximum file size in megabytes.
    | Files larger than this will be rejected.
    |
    */

    'max_file_size' => env('COMPARISON_MAX_FILE_SIZE', 50),

    /*
    |--------------------------------------------------------------------------
    | Sample Row Count
    |--------------------------------------------------------------------------
    |
    | Number of sample rows to show in preview and send to AI.
    | Smaller samples reduce API costs.
    |
    */

    'sample_row_count' => env('COMPARISON_SAMPLE_ROWS', 10),

    /*
    |--------------------------------------------------------------------------
    | Row Joining Strategies
    |--------------------------------------------------------------------------
    |
    | Available strategies for matching rows between files.
    |
    */

    'join_strategies' => [
        'inner_join' => 'Inner Join - Only matched rows',
        'left_join' => 'Left Join - All from file 1 + matches',
        'full_join' => 'Full Join - All rows from both files',
        'compare_all' => 'Compare All - Row by row comparison',
    ],

];
