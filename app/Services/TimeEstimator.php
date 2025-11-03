<?php

namespace App\Services;

class TimeEstimator
{
    /**
     * Estimate processing time for comparison.
     *
     * @param int $file1RowCount
     * @param int $file2RowCount
     * @param string $aiModel
     * @param bool $enableAi
     * @return array ['estimated_seconds' => X, 'message' => '...', 'breakdown' => [...]]
     */
    public function estimateProcessingTime(
        int $file1RowCount, 
        int $file2RowCount, 
        string $aiModel = 'openai/gpt-4o-mini',
        bool $enableAi = true
    ): array {
        $breakdown = [];
        $totalSeconds = 0;
        
        // Phase 1: Schema detection (lightweight)
        $schemaTime = 2; // ~2 seconds
        $breakdown['schema_detection'] = $schemaTime;
        $totalSeconds += $schemaTime;
        
        // Phase 2: Row joining
        $totalRows = $file1RowCount + $file2RowCount;
        $joiningTime = $this->estimateJoiningTime($totalRows);
        $breakdown['row_joining'] = $joiningTime;
        $totalSeconds += $joiningTime;
        
        // Phase 3: PHP structural analysis
        $analysisTime = $this->estimateAnalysisTime($file1RowCount, $file2RowCount);
        $breakdown['php_analysis'] = $analysisTime;
        $totalSeconds += $analysisTime;
        
        // Phase 4: AI processing (if enabled)
        if ($enableAi) {
            $aiTime = $this->estimateAiTime($file1RowCount, $file2RowCount, $aiModel);
            $breakdown['ai_analysis'] = $aiTime;
            $totalSeconds += $aiTime;
        }
        
        // Add buffer (10%)
        $totalSeconds = ceil($totalSeconds * 1.1);
        
        return [
            'estimated_seconds' => $totalSeconds,
            'message' => $this->formatTimeMessage($totalSeconds),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Estimate row joining time.
     *
     * @param int $totalRows
     * @return int Seconds
     */
    protected function estimateJoiningTime(int $totalRows): int
    {
        // ~1 second per 1000 rows
        return max(1, ceil($totalRows / 1000));
    }

    /**
     * Estimate PHP analysis time.
     *
     * @param int $file1RowCount
     * @param int $file2RowCount
     * @return int Seconds
     */
    protected function estimateAnalysisTime(int $file1RowCount, int $file2RowCount): int
    {
        $totalRows = $file1RowCount + $file2RowCount;
        
        // ~1-3 seconds per 1000 rows
        return max(2, ceil($totalRows / 500));
    }

    /**
     * Estimate AI processing time.
     *
     * @param int $file1RowCount
     * @param int $file2RowCount
     * @param string $aiModel
     * @return int Seconds
     */
    protected function estimateAiTime(int $file1RowCount, int $file2RowCount, string $aiModel): int
    {
        $totalRows = $file1RowCount + $file2RowCount;
        
        // Base time by model
        $baseTime = match (true) {
            str_contains($aiModel, 'gpt-4o-mini') => 10,
            str_contains($aiModel, 'gpt-4o') => 15,
            str_contains($aiModel, 'gpt-4') => 20,
            str_contains($aiModel, 'claude') => 12,
            default => 15,
        };
        
        // Add time based on data size
        // AI receives sample data, not all rows
        $sampleSize = min($totalRows, 100); // Max 100 rows sent to AI
        $additionalTime = ceil($sampleSize / 20);
        
        return $baseTime + $additionalTime;
    }

    /**
     * Format time estimate as Persian message.
     *
     * @param int $seconds
     * @return string
     */
    protected function formatTimeMessage(int $seconds): string
    {
        if ($seconds < 60) {
            return sprintf('تقریباً %d ثانیه', $seconds);
        }
        
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        
        if ($remainingSeconds > 0) {
            return sprintf('تقریباً %d دقیقه و %d ثانیه', $minutes, $remainingSeconds);
        }
        
        return sprintf('تقریباً %d دقیقه', $minutes);
    }

    /**
     * Get estimated time for a specific phase.
     *
     * @param string $phase 'schema' | 'joining' | 'analysis' | 'ai'
     * @param array $params
     * @return int Seconds
     */
    public function estimatePhaseTime(string $phase, array $params = []): int
    {
        return match ($phase) {
            'schema' => 2,
            'joining' => $this->estimateJoiningTime($params['total_rows'] ?? 0),
            'analysis' => $this->estimateAnalysisTime(
                $params['file1_rows'] ?? 0, 
                $params['file2_rows'] ?? 0
            ),
            'ai' => $this->estimateAiTime(
                $params['file1_rows'] ?? 0,
                $params['file2_rows'] ?? 0,
                $params['ai_model'] ?? 'openai/gpt-4o-mini'
            ),
            default => 0,
        };
    }
}
