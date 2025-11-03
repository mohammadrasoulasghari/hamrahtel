<?php

namespace App\Jobs;

use App\Models\Comparison;
use App\Services\ColumnDetectionService;
use App\Services\RowJoiningService;
use App\Services\ComparisonAnalyzer;
use App\Services\OpenRouterService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ProcessFileComparison implements ShouldQueue
{
    use Queueable;

    public $timeout = 600; // 10 minutes timeout
    public $tries = 1; // Only try once
    
    protected $comparisonId;
    protected $keyColumns;
    protected $joinStrategy;
    protected $description;

    /**
     * Create a new job instance.
     */
    public function __construct(int $comparisonId, array $keyColumns, string $joinStrategy, ?string $description = null)
    {
        $this->comparisonId = $comparisonId;
        $this->keyColumns = $keyColumns;
        $this->joinStrategy = $joinStrategy;
        $this->description = $description;
    }

    /**
     * Execute the job.
     */
    public function handle(
        RowJoiningService $rowJoiningService,
        ComparisonAnalyzer $comparisonAnalyzer,
        OpenRouterService $openRouterService
    ): void
    {
        try {
            $comparison = Comparison::findOrFail($this->comparisonId);
            
            // Update status
            $comparison->update([
                'processing_started_at' => now(),
                'selected_key_columns' => $this->keyColumns,
                'row_join_strategy' => $this->joinStrategy,
            ]);

            $file1Path = storage_path('app/public/' . $comparison->file1_path);
            $file2Path = storage_path('app/public/' . $comparison->file2_path);

            // Phase 1: Load data in chunks
            Log::info("ProcessFileComparison: Loading files in chunks");
            $file1Data = $this->loadFileInChunks($file1Path);
            $file2Data = $this->loadFileInChunks($file2Path);

            // Phase 2: Perform row joining
            Log::info("ProcessFileComparison: Performing row joining");
            if ($this->joinStrategy === 'compare_all') {
                $joinResult = $rowJoiningService->compareAll($file1Data, $file2Data);
            } else {
                $joinResult = $rowJoiningService->joinByKeyColumns(
                    $file1Data,
                    $file2Data,
                    $this->keyColumns,
                    $this->joinStrategy
                );
            }

            // Update matched counts
            $comparison->update([
                'matched_count' => count($joinResult['matched']),
                'unmatched_file1_count' => count($joinResult['unmatched_file1']),
                'unmatched_file2_count' => count($joinResult['unmatched_file2']),
            ]);

            // Phase 3: PHP structural analysis
            Log::info("ProcessFileComparison: Running PHP analysis");
            $phpAnalysis = $comparisonAnalyzer->analyzeStructure(
                $file1Data,
                $file2Data,
                $joinResult
            );

            $comparison->update([
                'php_analysis_result' => $phpAnalysis,
            ]);

            // Phase 4: AI analysis (optional)
            if (config('comparison.enable_ai_analysis', true)) {
                Log::info("ProcessFileComparison: Running AI analysis");
                $aiResult = $openRouterService->compareFiles(
                    $file1Path,
                    $file2Path,
                    $this->description,
                    $phpAnalysis
                );

                $comparison->update([
                    'result' => $aiResult,
                    'processing_completed_at' => now(),
                ]);
            } else {
                $comparison->update([
                    'result' => 'تحلیل PHP به پایان رسید. تحلیل هوش مصنوعی غیرفعال است.',
                    'processing_completed_at' => now(),
                ]);
            }

            Log::info("ProcessFileComparison: Completed successfully for comparison {$this->comparisonId}");

        } catch (\Exception $e) {
            Log::error("ProcessFileComparison failed: " . $e->getMessage());
            
            $comparison = Comparison::find($this->comparisonId);
            if ($comparison) {
                $comparison->update([
                    'result' => 'خطا در پردازش: ' . $e->getMessage(),
                    'processing_completed_at' => now(),
                ]);
            }
            
            throw $e;
        }
    }

    /**
     * Load Excel file in chunks to avoid memory exhaustion.
     * Uses true chunked reading without loading entire file.
     *
     * @param string $filePath
     * @return array
     */
    protected function loadFileInChunks(string $filePath): array
    {
        $data = [];
        $chunkSize = config('comparison.chunk_size', 500);
        $isFirstChunk = true;
        
        Excel::chunk($filePath, $chunkSize, function($rows) use (&$data, &$isFirstChunk) {
            foreach ($rows as $index => $row) {
                // Skip header row (first row of first chunk)
                if ($isFirstChunk && $index === 0) {
                    $isFirstChunk = false;
                    continue;
                }
                
                $data[] = is_array($row) ? $row : $row->toArray();
            }
            
            if ($isFirstChunk) {
                $isFirstChunk = false;
            }
        });
        
        return $data;
    }
}
