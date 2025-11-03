<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('comparisons', function (Blueprint $table) {
            // Schema metadata
            $table->json('file1_columns')->nullable()->after('file2_json');
            $table->json('file2_columns')->nullable()->after('file1_columns');
            
            // Matching configuration
            $table->json('selected_key_columns')->nullable()->after('file2_columns');
            $table->string('row_join_strategy')->nullable()->after('selected_key_columns');
            
            // Matching results
            $table->integer('matched_count')->default(0)->after('row_join_strategy');
            $table->integer('unmatched_file1_count')->default(0)->after('matched_count');
            $table->integer('unmatched_file2_count')->default(0)->after('unmatched_file1_count');
            
            // PHP analysis results
            $table->json('php_analysis_result')->nullable()->after('unmatched_file2_count');
            
            // Processing timestamps
            $table->timestamp('processing_started_at')->nullable()->after('php_analysis_result');
            $table->timestamp('processing_completed_at')->nullable()->after('processing_started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comparisons', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
