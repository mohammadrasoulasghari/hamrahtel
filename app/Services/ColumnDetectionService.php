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
     * Uses ReadFilter to read only first row without loading entire file.
     *
     * @param string $filePath
     * @return array
     */
    protected function extractColumnNames(string $filePath): array
    {
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            
            // Create filter to read only first row
            $chunkFilter = new ChunkReadFilter();
            $chunkFilter->setRows(1, 1);
            $reader->setReadFilter($chunkFilter);
            
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $firstRow = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1')[0];
            
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            
            if (empty($firstRow)) {
                return [];
            }
            
            Log::info('First row raw data: ' . json_encode($firstRow));
            
            // Check if headers look like actual text (not empty, not just numbers)
            $hasRealHeaders = false;
            $nonEmptyCount = 0;
            $textHeaderCount = 0;
            
            foreach ($firstRow as $cell) {
                if ($cell !== null && $cell !== '') {
                    $nonEmptyCount++;
                    // Check if it's a meaningful text header (not just a number)
                    if (!is_numeric($cell) || mb_strlen(trim((string)$cell)) > 3) {
                        $textHeaderCount++;
                    }
                }
            }
            
            // If we have at least 50% non-empty cells and at least 50% text headers, use them
            $hasRealHeaders = ($nonEmptyCount > count($firstRow) / 2) && ($textHeaderCount > $nonEmptyCount / 2);
            
            Log::info("Header detection: nonEmpty={$nonEmptyCount}, textHeaders={$textHeaderCount}, hasReal={$hasRealHeaders}");
            
            if ($hasRealHeaders) {
                $columnNames = [];
                foreach ($firstRow as $value) {
                    if ($value === null || $value === '') {
                        $columnNames[] = 'ستون_' . (count($columnNames) + 1);
                    } else {
                        $cleanName = $this->cleanColumnName($value);
                        $columnNames[] = $cleanName ?: 'ستون_' . (count($columnNames) + 1);
                    }
                }
                Log::info('Using real headers: ' . json_encode($columnNames));
                return $columnNames;
            }
            
            // Otherwise use generic column names
            $genericNames = array_map(fn($i) => 'ستون_' . ($i + 1), array_keys($firstRow));
            Log::info('Using generic names: ' . json_encode($genericNames));
            return $genericNames;
            
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
     * Uses ReadFilter to read only limited rows without loading entire file.
     *
     * @param string $filePath
     * @param int $limit Number of rows to return
     * @return array
     */
    public function getSampleRows(string $filePath, int $limit = 5): array
    {
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            
            // Create filter to read only sample rows (skip header)
            $chunkFilter = new ChunkReadFilter();
            $chunkFilter->setRows(2, $limit); // Start from row 2, read $limit rows
            $reader->setReadFilter($chunkFilter);
            
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            
            // Get all loaded rows
            $rows = [];
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row);
                if (!empty($rowData[0])) {
                    $rows[] = $rowData[0];
                }
            }
            
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            
            return $rows;
        } catch (\Exception $e) {
            Log::error('Error getting sample rows: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Estimate row count without loading entire file.
     * Uses ReadFilter to read only first column for counting.
     *
     * @param string $filePath
     * @return int
     */
    public function estimateRowCount(string $filePath): int
    {
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            
            // Don't use filter here - we need metadata only
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            
            $rowCount = $sheet->getHighestRow() - 1; // Subtract header row
            
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            
            return max(0, $rowCount);
        } catch (\Exception $e) {
            Log::error('Error estimating row count: ' . $e->getMessage());
            return 0;
        }
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
