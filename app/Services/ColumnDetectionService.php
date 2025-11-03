<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ColumnDetectionService
{
    /**
     * Detect columns from a file without loading the entire content.
     *
     * @param string $filePath Absolute path to the Excel/CSV file
     * @return array ['columns' => [...], 'row_count' => X, 'data_types' => [...], 'sample_data' => [...]]
     */
    public function detectColumns(string $filePath): array
    {
        try {
            // Get column names from header row
            $columns = $this->extractColumnNames($filePath);
            
            // Get sample rows to infer data types
            $sampleRows = $this->getSampleRows($filePath, 10);
            
            // Estimate total row count
            $rowCount = $this->estimateRowCount($filePath);
            
            // Infer data types for each column
            $dataTypes = $this->inferDataTypes($columns, $sampleRows);
            
            return [
                'columns' => $columns,
                'row_count' => $rowCount,
                'data_types' => $dataTypes,
                'sample_data' => array_slice($sampleRows, 0, 5), // Return first 5 for preview
            ];
        } catch (\Exception $e) {
            Log::error('ColumnDetectionService error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Extract column names from the file header.
     * Handles numeric column names (0, 1, 2...) by using first row as headers.
     *
     * @param string $filePath
     * @return array
     */
    protected function extractColumnNames(string $filePath): array
    {
        try {
            $collection = Excel::toCollection(null, $filePath);
            
            if ($collection->isEmpty() || $collection->first()->isEmpty()) {
                return [];
            }
            
            $sheet = $collection->first();
            $firstRow = $sheet->first();
            
            if (!$firstRow) {
                return [];
            }
            
            // Convert first row to array
            $firstRowArray = is_array($firstRow) ? $firstRow : $firstRow->toArray();
            
            // Check if headers are numeric (0, 1, 2...) which means no header row
            $hasNumericHeaders = false;
            if (!empty($firstRowArray)) {
                $keys = array_keys($firstRowArray);
                $hasNumericHeaders = is_numeric($keys[0]);
            }
            
            // If headers are numeric, use first row values as column names
            if ($hasNumericHeaders && !empty($firstRowArray)) {
                $columnNames = [];
                foreach ($firstRowArray as $value) {
                    // Clean and use the value as column name
                    $cleanName = $this->cleanColumnName($value);
                    $columnNames[] = $cleanName ?: 'ستون_' . (count($columnNames) + 1);
                }
                return $columnNames;
            }
            
            // Otherwise use the keys (default Laravel Excel behavior)
            return array_keys($firstRowArray);
            
        } catch (\Exception $e) {
            Log::error('Error extracting column names: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Clean column name for display.
     *
     * @param mixed $value
     * @return string
     */
    protected function cleanColumnName($value): string
    {
        if (is_null($value) || $value === '') {
            return '';
        }
        
        $name = trim((string)$value);
        
        // Remove special characters but keep Persian and English letters
        $name = preg_replace('/[^\p{L}\p{N}_\s\-]/u', '', $name);
        
        // Replace spaces with underscores
        $name = preg_replace('/\s+/', '_', $name);
        
        return $name;
    }

    /**
     * Get sample rows from the file (lightweight read).
     *
     * @param string $filePath
     * @param int $limit Number of rows to return
     * @return array
     */
    public function getSampleRows(string $filePath, int $limit = 5): array
    {
        $collection = Excel::toCollection(null, $filePath);
        
        if ($collection->isEmpty()) {
            return [];
        }
        
        $sheet = $collection->first();
        $rows = $sheet->take($limit + 1)->toArray(); // +1 for header
        
        // Remove header row
        array_shift($rows);
        
        return $rows;
    }

    /**
     * Estimate row count without loading entire file.
     * For now, we load and count, but this can be optimized with streaming.
     *
     * @param string $filePath
     * @return int
     */
    public function estimateRowCount(string $filePath): int
    {
        $collection = Excel::toCollection(null, $filePath);
        
        if ($collection->isEmpty()) {
            return 0;
        }
        
        // Subtract 1 for header row
        return $collection->first()->count() - 1;
    }

    /**
     * Infer data types for each column based on sample data.
     *
     * @param array $columns
     * @param array $sampleRows
     * @return array ['column_name' => 'data_type', ...]
     */
    protected function inferDataTypes(array $columns, array $sampleRows): array
    {
        $dataTypes = [];
        
        foreach ($columns as $index => $column) {
            $samples = [];
            
            foreach ($sampleRows as $row) {
                if (isset($row[$index])) {
                    $samples[] = $row[$index];
                }
            }
            
            $dataTypes[$column] = $this->inferDataType($samples);
        }
        
        return $dataTypes;
    }

    /**
     * Infer data type from sample values.
     *
     * @param array $samples
     * @return string 'string' | 'integer' | 'decimal' | 'date' | 'boolean'
     */
    public function inferDataType(array $samples): string
    {
        if (empty($samples)) {
            return 'string';
        }
        
        $nonNullSamples = array_filter($samples, fn($val) => $val !== null && $val !== '');
        
        if (empty($nonNullSamples)) {
            return 'string';
        }
        
        $isInteger = true;
        $isDecimal = true;
        $isBoolean = true;
        $isDate = true;
        
        foreach ($nonNullSamples as $value) {
            // Check boolean
            if ($isBoolean && !in_array(strtolower($value), ['true', 'false', '0', '1', 'yes', 'no', 'بله', 'خیر'], true)) {
                $isBoolean = false;
            }
            
            // Check integer
            if ($isInteger && !ctype_digit((string)$value)) {
                $isInteger = false;
            }
            
            // Check decimal
            if ($isDecimal && !is_numeric($value)) {
                $isDecimal = false;
            }
            
            // Check date (basic patterns)
            if ($isDate && !$this->looksLikeDate($value)) {
                $isDate = false;
            }
        }
        
        if ($isBoolean) {
            return 'boolean';
        }
        
        if ($isInteger) {
            return 'integer';
        }
        
        if ($isDecimal) {
            return 'decimal';
        }
        
        if ($isDate) {
            return 'date';
        }
        
        return 'string';
    }

    /**
     * Check if a value looks like a date.
     *
     * @param mixed $value
     * @return bool
     */
    protected function looksLikeDate($value): bool
    {
        if (!is_string($value)) {
            return false;
        }
        
        // Common date patterns
        $patterns = [
            '/^\d{4}-\d{2}-\d{2}$/',           // YYYY-MM-DD
            '/^\d{2}\/\d{2}\/\d{4}$/',         // DD/MM/YYYY
            '/^\d{4}\/\d{2}\/\d{2}$/',         // YYYY/MM/DD
            '/^\d{2}-\d{2}-\d{4}$/',           // DD-MM-YYYY
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }
        
        // Try parsing with strtotime
        return strtotime($value) !== false;
    }
}
