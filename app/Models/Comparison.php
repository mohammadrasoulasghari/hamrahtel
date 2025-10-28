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
    ];

    protected $casts = [
        'file1_json' => 'array',
        'file2_json' => 'array',
    ];
}
