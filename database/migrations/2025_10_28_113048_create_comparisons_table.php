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
        Schema::create('comparisons', function (Blueprint $table) {
            $table->id();
            $table->string('file1_path');
            $table->string('file2_path');
            $table->string('file1_original_name');
            $table->string('file2_original_name');
            $table->longText('file1_json')->nullable();
            $table->longText('file2_json')->nullable();
            $table->longText('description')->nullable();
            $table->longText('ai_result')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comparisons');
    }
};
