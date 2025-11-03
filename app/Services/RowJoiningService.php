<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class RowJoiningService
{
    /**
     * Join two datasets by key columns (like SQL JOIN).
     *
     * @param array $data1 First dataset
     * @param array $data2 Second dataset
     * @param array $keyColumns Column names to use as join keys
     * @param string $strategy 'inner_join' | 'left_join' | 'full_join'
     * @return array ['matched' => [...], 'unmatched_file1' => [...], 'unmatched_file2' => [...]]
     */
    public function joinByKeyColumns(array $data1, array $data2, array $keyColumns, string $strategy = 'inner_join'): array
    {
        $matched = [];
        $unmatchedFile1 = [];
        $unmatchedFile2 = [];
        
        // Build index for file2 for faster lookups
        $file2Index = $this->buildIndex($data2, $keyColumns);
        $matchedFile2Indices = [];
        
        // Match rows from file1
        foreach ($data1 as $row1) {
            $keyValue = $this->extractKeyValue($row1, $keyColumns);
            
            if ($keyValue !== null && isset($file2Index[$keyValue])) {
                // Match found
                foreach ($file2Index[$keyValue] as $index => $row2) {
                    $matched[] = [
                        'file1' => $row1,
                        'file2' => $row2,
                        'key' => $keyValue,
                    ];
                    $matchedFile2Indices[$index] = true;
                }
            } else {
                // No match for this row from file1
                if (in_array($strategy, ['left_join', 'full_join'])) {
                    $unmatchedFile1[] = $row1;
                }
            }
        }
        
        // Find unmatched rows from file2
        if (in_array($strategy, ['full_join'])) {
            foreach ($data2 as $index => $row2) {
                if (!isset($matchedFile2Indices[$index])) {
                    $unmatchedFile2[] = $row2;
                }
            }
        }
        
        return [
            'matched' => $matched,
            'unmatched_file1' => $unmatchedFile1,
            'unmatched_file2' => $unmatchedFile2,
        ];
    }

    /**
     * Build an index of rows by key columns for faster lookups.
     *
     * @param array $data
     * @param array $keyColumns
     * @return array ['key_value' => [row_index => row, ...], ...]
     */
    protected function buildIndex(array $data, array $keyColumns): array
    {
        $index = [];
        
        foreach ($data as $rowIndex => $row) {
            $keyValue = $this->extractKeyValue($row, $keyColumns);
            
            if ($keyValue !== null) {
                if (!isset($index[$keyValue])) {
                    $index[$keyValue] = [];
                }
                $index[$keyValue][$rowIndex] = $row;
            }
        }
        
        return $index;
    }

    /**
     * Extract key value from a row by combining specified columns.
     *
     * @param array $row
     * @param array $keyColumns
     * @return string|null Combined key value, or null if any key column is missing
     */
    protected function extractKeyValue(array $row, array $keyColumns): ?string
    {
        $keyParts = [];
        
        foreach ($keyColumns as $column) {
            if (!isset($row[$column]) || $row[$column] === null || $row[$column] === '') {
                return null; // Key column missing or empty
            }
            $keyParts[] = (string)$row[$column];
        }
        
        return implode('||', $keyParts);
    }

    /**
     * Extract only specified columns from dataset.
     *
     * @param array $data
     * @param array $columnNames
     * @return array Filtered dataset
     */
    public function extractColumns(array $data, array $columnNames): array
    {
        $filtered = [];
        
        foreach ($data as $row) {
            $filteredRow = [];
            
            foreach ($columnNames as $column) {
                $filteredRow[$column] = $row[$column] ?? null;
            }
            
            $filtered[] = $filteredRow;
        }
        
        return $filtered;
    }

    /**
     * Find matching row in file2 data for a given file1 row.
     *
     * @param array $file1Row
     * @param array $file2Data
     * @param array $keyColumns
     * @return array|null Matching row from file2, or null
     */
    public function findMatchingRow(array $file1Row, array $file2Data, array $keyColumns): ?array
    {
        $keyValue1 = $this->extractKeyValue($file1Row, $keyColumns);
        
        if ($keyValue1 === null) {
            return null;
        }
        
        foreach ($file2Data as $row2) {
            $keyValue2 = $this->extractKeyValue($row2, $keyColumns);
            
            if ($keyValue1 === $keyValue2) {
                return $row2;
            }
        }
        
        return null;
    }

    /**
     * Compare all rows from both files 1:1 (no key matching).
     *
     * @param array $data1
     * @param array $data2
     * @return array ['matched' => [...], 'unmatched_file1' => [...], 'unmatched_file2' => [...]]
     */
    public function compareAll(array $data1, array $data2): array
    {
        $matched = [];
        $unmatchedFile1 = [];
        $unmatchedFile2 = [];
        
        $maxRows = max(count($data1), count($data2));
        
        for ($i = 0; $i < $maxRows; $i++) {
            if (isset($data1[$i]) && isset($data2[$i])) {
                $matched[] = [
                    'file1' => $data1[$i],
                    'file2' => $data2[$i],
                    'index' => $i,
                ];
            } elseif (isset($data1[$i])) {
                $unmatchedFile1[] = $data1[$i];
            } elseif (isset($data2[$i])) {
                $unmatchedFile2[] = $data2[$i];
            }
        }
        
        return [
            'matched' => $matched,
            'unmatched_file1' => $unmatchedFile1,
            'unmatched_file2' => $unmatchedFile2,
        ];
    }
}
