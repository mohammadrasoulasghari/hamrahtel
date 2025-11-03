<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comparison extends Model
{
    protected $fillable = [
        'file1_path',
        'file2_path',
        'file1_original_name',
        'file2_original_name',
        'file1_json',
        'file2_json',
        'description',
        'ai_result',
        'status',
        'file1_columns',
        'file2_columns',
        'selected_key_columns',
        'row_join_strategy',
        'matched_count',
        'unmatched_file1_count',
        'unmatched_file2_count',
        'php_analysis_result',
        'processing_started_at',
        'processing_completed_at',
    ];

    protected $casts = [
        'file1_json' => 'array',
        'file2_json' => 'array',
        'file1_columns' => 'array',
        'file2_columns' => 'array',
        'selected_key_columns' => 'array',
        'php_analysis_result' => 'array',
        'processing_started_at' => 'datetime',
        'processing_completed_at' => 'datetime',
    ];
}
