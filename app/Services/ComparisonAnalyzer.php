<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ComparisonAnalyzer
{
    /**
     * Analyze structure and detect differences between two datasets.
     *
     * @param array $schema1 Schema from file 1
     * @param array $schema2 Schema from file 2
     * @param array $matchedPairs Matched row pairs
     * @param array $unmatchedFile1 Unmatched rows from file 1
     * @param array $unmatchedFile2 Unmatched rows from file 2
     * @return array Structured analysis result
     */
    public function analyzeStructure(array $schema1, array $schema2, array $matchedPairs, array $unmatchedFile1, array $unmatchedFile2): array
    {
        $analysis = [
            'schema_differences' => $this->detectColumnDifferences($schema1['columns'], $schema2['columns']),
            'row_count_analysis' => [
                'file1_total' => $schema1['row_count'],
                'file2_total' => $schema2['row_count'],
                'matched_count' => count($matchedPairs),
                'unmatched_file1_count' => count($unmatchedFile1),
                'unmatched_file2_count' => count($unmatchedFile2),
            ],
            'matched_rows_analysis' => $this->analyzeMatchedRows($matchedPairs),
            'data_quality' => $this->analyzeDataQuality($matchedPairs, $unmatchedFile1, $unmatchedFile2),
        ];
        
        return $analysis;
    }

    /**
     * Detect column differences between two schemas.
     *
     * @param array $columns1
     * @param array $columns2
     * @return array
     */
    public function detectColumnDifferences(array $columns1, array $columns2): array
    {
        $added = array_diff($columns2, $columns1);
        $removed = array_diff($columns1, $columns2);
        $common = array_intersect($columns1, $columns2);
        
        return [
            'added_columns' => array_values($added),
            'removed_columns' => array_values($removed),
            'common_columns' => array_values($common),
            'total_file1' => count($columns1),
            'total_file2' => count($columns2),
        ];
    }

    /**
     * Analyze matched row pairs for value differences.
     *
     * @param array $matchedPairs
     * @return array
     */
    public function analyzeMatchedRows(array $matchedPairs): array
    {
        if (empty($matchedPairs)) {
            return [
                'total_differences' => 0,
                'columns_with_differences' => [],
                'sample_differences' => [],
            ];
        }
        
        $columnDifferences = [];
        $sampleDifferences = [];
        $totalDifferences = 0;
        
        foreach ($matchedPairs as $index => $pair) {
            $row1 = $pair['file1'];
            $row2 = $pair['file2'];
            
            $rowDifferences = $this->compareRows($row1, $row2);
            
            if (!empty($rowDifferences)) {
                $totalDifferences++;
                
                foreach ($rowDifferences as $column => $diff) {
                    if (!isset($columnDifferences[$column])) {
                        $columnDifferences[$column] = 0;
                    }
                    $columnDifferences[$column]++;
                }
                
                // Keep sample of first 10 differences
                if (count($sampleDifferences) < 10) {
                    $sampleDifferences[] = [
                        'row_index' => $index,
                        'key' => $pair['key'] ?? $index,
                        'differences' => $rowDifferences,
                    ];
                }
            }
        }
        
        return [
            'total_differences' => $totalDifferences,
            'columns_with_differences' => $columnDifferences,
            'sample_differences' => $sampleDifferences,
            'percentage_different' => count($matchedPairs) > 0 
                ? round(($totalDifferences / count($matchedPairs)) * 100, 2) 
                : 0,
        ];
    }

    /**
     * Compare two rows and detect differences.
     *
     * @param array $row1
     * @param array $row2
     * @return array ['column_name' => ['file1' => value1, 'file2' => value2], ...]
     */
    protected function compareRows(array $row1, array $row2): array
    {
        $differences = [];
        
        $allColumns = array_unique(array_merge(array_keys($row1), array_keys($row2)));
        
        foreach ($allColumns as $column) {
            $value1 = $row1[$column] ?? null;
            $value2 = $row2[$column] ?? null;
            
            // Normalize for comparison
            $normalizedValue1 = $this->normalizeValue($value1);
            $normalizedValue2 = $this->normalizeValue($value2);
            
            if ($normalizedValue1 !== $normalizedValue2) {
                $differences[$column] = [
                    'file1' => $value1,
                    'file2' => $value2,
                ];
            }
        }
        
        return $differences;
    }

    /**
     * Normalize a value for comparison.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function normalizeValue($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        
        if (is_string($value)) {
            return trim($value);
        }
        
        return $value;
    }

    /**
     * Analyze data quality issues.
     *
     * @param array $matchedPairs
     * @param array $unmatchedFile1
     * @param array $unmatchedFile2
     * @return array
     */
    protected function analyzeDataQuality(array $matchedPairs, array $unmatchedFile1, array $unmatchedFile2): array
    {
        $nullCounts = [];
        $emptyCounts = [];
        
        // Analyze matched pairs
        foreach ($matchedPairs as $pair) {
            foreach ($pair['file1'] as $column => $value) {
                if (!isset($nullCounts[$column])) {
                    $nullCounts[$column] = ['file1' => 0, 'file2' => 0];
                    $emptyCounts[$column] = ['file1' => 0, 'file2' => 0];
                }
                
                if ($value === null) {
                    $nullCounts[$column]['file1']++;
                }
                if ($value === '') {
                    $emptyCounts[$column]['file1']++;
                }
            }
            
            foreach ($pair['file2'] as $column => $value) {
                if (!isset($nullCounts[$column])) {
                    $nullCounts[$column] = ['file1' => 0, 'file2' => 0];
                    $emptyCounts[$column] = ['file1' => 0, 'file2' => 0];
                }
                
                if ($value === null) {
                    $nullCounts[$column]['file2']++;
                }
                if ($value === '') {
                    $emptyCounts[$column]['file2']++;
                }
            }
        }
        
        return [
            'null_counts' => $nullCounts,
            'empty_counts' => $emptyCounts,
            'data_completeness' => $this->calculateCompleteness($nullCounts, $emptyCounts, count($matchedPairs)),
        ];
    }

    /**
     * Calculate data completeness percentage.
     *
     * @param array $nullCounts
     * @param array $emptyCounts
     * @param int $totalRows
     * @return array
     */
    protected function calculateCompleteness(array $nullCounts, array $emptyCounts, int $totalRows): array
    {
        $completeness = [];
        
        if ($totalRows === 0) {
            return $completeness;
        }
        
        foreach ($nullCounts as $column => $counts) {
            $totalNullEmpty = ($counts['file1'] + $counts['file2'] + 
                              ($emptyCounts[$column]['file1'] ?? 0) + 
                              ($emptyCounts[$column]['file2'] ?? 0));
            
            $completenessPercentage = 100 - (($totalNullEmpty / ($totalRows * 2)) * 100);
            
            $completeness[$column] = round($completenessPercentage, 2);
        }
        
        return $completeness;
    }

    /**
     * Generate a structured report from analysis.
     *
     * @param array $analysis
     * @return array
     */
    public function generateStructuralReport(array $analysis): array
    {
        $findings = [];
        
        // Schema findings
        if (!empty($analysis['schema_differences']['added_columns'])) {
            $findings[] = [
                'category' => 'SCHEMA_DIFF',
                'severity' => 'info',
                'message' => 'ستون‌های جدید در فایل دوم',
                'details' => $analysis['schema_differences']['added_columns'],
            ];
        }
        
        if (!empty($analysis['schema_differences']['removed_columns'])) {
            $findings[] = [
                'category' => 'SCHEMA_DIFF',
                'severity' => 'warning',
                'message' => 'ستون‌های حذف شده از فایل اول',
                'details' => $analysis['schema_differences']['removed_columns'],
            ];
        }
        
        // Row count findings
        $rowCountDiff = abs($analysis['row_count_analysis']['file1_total'] - $analysis['row_count_analysis']['file2_total']);
        if ($rowCountDiff > 0) {
            $findings[] = [
                'category' => 'ROW_COUNT_DIFF',
                'severity' => 'warning',
                'message' => sprintf('تفاوت %d ردیف بین دو فایل', $rowCountDiff),
                'details' => $analysis['row_count_analysis'],
            ];
        }
        
        // Value differences
        if ($analysis['matched_rows_analysis']['total_differences'] > 0) {
            $findings[] = [
                'category' => 'VALUE_DIFF',
                'severity' => 'high',
                'message' => sprintf('%d ردیف با مقادیر متفاوت', $analysis['matched_rows_analysis']['total_differences']),
                'details' => $analysis['matched_rows_analysis'],
            ];
        }
        
        // Missing rows
        if ($analysis['row_count_analysis']['unmatched_file1_count'] > 0 || 
            $analysis['row_count_analysis']['unmatched_file2_count'] > 0) {
            $findings[] = [
                'category' => 'MISSING_ROWS',
                'severity' => 'warning',
                'message' => 'ردیف‌های بدون جفت',
                'details' => [
                    'unmatched_file1' => $analysis['row_count_analysis']['unmatched_file1_count'],
                    'unmatched_file2' => $analysis['row_count_analysis']['unmatched_file2_count'],
                ],
            ];
        }
        
        return [
            'findings' => $findings,
            'summary' => [
                'total_findings' => count($findings),
                'analysis_timestamp' => now()->toIso8601String(),
            ],
        ];
    }
}
