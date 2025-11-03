# Tasks for Smart Column Matching & Optimized Comparison

## 1. Schema Discovery & Column Detection

-   [ ] 1.1 Create `ColumnDetectionService` - analyze file schemas
-   [ ] 1.2 Add method `detectColumns($filePath)` - returns column names, types, uniqueness
-   [ ] 1.3 Implement data type inference (string, number, date, boolean)
-   [ ] 1.4 Add method `estimateRowCount($filePath)` - without loading entire file
-   [ ] 1.5 Add method `getSampleRows($filePath, $limit)` - stream first N rows
-   [ ] 1.6 Create database migration - add `file1_columns`, `file2_columns` JSON to comparisons table

## 2. Column Matching UI & API

-   [ ] 2.1 Create route `POST /api/preview-columns` - accept file, return schema
-   [ ] 2.2 Implement `previewColumns()` method in ComparisonController
-   [ ] 2.3 Create Blade component `column-selector.blade.php` - multi-select UI
-   [ ] 2.4 Add JavaScript to populate column options dynamically
-   [ ] 2.5 Create route `POST /api/validate-matching` - preview matched rows
-   [ ] 2.6 Add method `validateColumnMatch($file1, $file2, $keyColumns)` - test join logic

## 3. Row Joining & Filtering

-   [ ] 3.1 Create `RowJoiningService` - implement join-like logic
-   [ ] 3.2 Method `joinByKeyColumns($data1, $data2, $keyColumns)` - match rows by key values
-   [ ] 3.3 Method `extractColumns($data, $columnNames)` - filter to specific columns
-   [ ] 3.4 Method `findMatchingRows($file1Row, $file2Data, $keyColumns)` - locate pairs
-   [ ] 3.5 Add strategy pattern - support JOIN, LEFT JOIN, INNER JOIN behaviors
-   [ ] 3.6 Return structured result: `{matched: [], unmatched_file1: [], unmatched_file2: []}`

## 4. PHP-Based Comparison Analysis

-   [ ] 4.1 Create `ComparisonAnalyzer` service - structural analysis
-   [ ] 4.2 Method `analyzeStructure($data1, $data2)` - detect schema differences
-   [ ] 4.3 Method `detectColumnDifferences($cols1, $cols2)` - added/removed/renamed columns
-   [ ] 4.4 Method `analyzeMatchedRows($pairs)` - detect row-level differences
-   [ ] 4.5 Method `generateStructuralReport($analysis)` - return categorized findings
-   [ ] 4.6 Categorize findings: SCHEMA_DIFF, ROW_COUNT_DIFF, VALUE_DIFF, MISSING_ROWS

## 5. Memory-Efficient File Processing

-   [ ] 5.1 Refactor `convertToJson()` in ComparisonController - add `$limit` parameter
-   [ ] 5.2 Implement streaming reader - process large files in chunks
-   [ ] 5.3 Add method `readFileChunked($filePath, $chunkSize)` - iterator
-   [ ] 5.4 Add configuration: `COMPARISON_MAX_ROWS` (default 5000 per file)
-   [ ] 5.5 Add configuration: `COMPARISON_CHUNK_SIZE` (default 500 rows)
-   [ ] 5.6 If file exceeds limit, show warning with sampling option

## 6. Time Estimation

-   [ ] 6.1 Create `TimeEstimator` service
-   [ ] 6.2 Method `estimateProcessingTime($fileSize, $rowCount, $aiModel)` - return seconds
-   [ ] 6.3 Base estimates: schema detection (~2s), joining (~1s per 1000 rows), AI (~5-30s)
-   [ ] 6.4 Add API endpoint `GET /api/estimate-time` - return estimated completion time
-   [ ] 6.5 Return format: `{estimated_seconds: 25, message: "تقریباً 25 ثانیه"}`

## 7. Progress Tracking & UI

-   [ ] 7.1 Add `progress_status` field to comparisons table (enum: preview, joining, analyzing, ai_processing, complete)
-   [ ] 7.2 Implement WebSocket/SSE for real-time progress updates _(optional Phase 2)_
-   [ ] 7.3 Create frontend component `progress-tracker.blade.php`
-   [ ] 7.4 Display: schema detection → joining → analysis → AI → complete
-   [ ] 7.5 Show percentage and current step message
-   [ ] 7.6 Add estimated time remaining

## 8. Refactor Upload Flow

-   [ ] 8.1 Split `upload()` into two stages: `preview()` and `compare()`
-   [ ] 8.2 New route `POST /upload` → returns `{file1_id, file2_id, preview_url}`
-   [ ] 8.3 Preview route `GET /compare-preview/{file1_id}/{file2_id}` → column selector UI
-   [ ] 8.4 Compare route `POST /start-comparison` → initiates full pipeline
-   [ ] 8.5 Update ComparisonController workflow accordingly
-   [ ] 8.6 Update views: `index.blade.php` → add preview stage

## 9. AI Service Optimization

-   [ ] 9.1 Refactor `buildComparisonPrompt()` - accept filtered data + metadata
-   [ ] 9.1 New method `buildOptimizedPrompt($phpAnalysis, $matchedRows, $unmatched, $userDescription)`
-   [ ] 9.3 Send to AI: only structural findings + sample of matched rows (not all rows)
-   [ ] 9.4 AI prompt focuses on: data quality assessment, semantic validation, insights
-   [ ] 9.5 Reduce max_tokens if using filtered data (reduce API costs)

## 10. Database & Configuration

-   [ ] 10.1 Create migration - add columns to comparisons table:
    -   `file1_columns` (JSON)
    -   `file2_columns` (JSON)
    -   `selected_key_columns` (JSON)
    -   `row_join_strategy` (string)
    -   `matched_count` (integer)
    -   `unmatched_file1_count` (integer)
    -   `unmatched_file2_count` (integer)
    -   `php_analysis_result` (JSON)
    -   `processing_started_at` (timestamp)
    -   `processing_completed_at` (timestamp)
-   [ ] 10.2 Create config file: `config/comparison.php`
    -   `max_rows_per_file` = 5000
    -   `chunk_size` = 500
    -   `enable_ai_analysis` = true
    -   `ai_model` = 'openai/gpt-4o-mini'
    -   `timeout` = 120

## 11. Testing & Validation

-   [ ] 11.1 Unit test: `ColumnDetectionService` - schema detection accuracy
-   [ ] 11.2 Unit test: `RowJoiningService` - join logic correctness
-   [ ] 11.3 Unit test: `ComparisonAnalyzer` - finding detection
-   [ ] 11.4 Integration test: Full pipeline with sample Excel files
-   [ ] 11.5 Performance test: Process 5000-row file without memory errors
-   [ ] 11.6 Test: Time estimation accuracy

## 12. Documentation & User Guide

-   [ ] 12.1 Update README.md with new workflow
-   [ ] 12.2 Add examples: joining by key column (e.g., "ln" or "ID")
-   [ ] 12.3 Document: Column selection strategies
-   [ ] 12.4 Add troubleshooting: file size limits, column matching tips
-   [ ] 12.5 Create user video/GIF: column selection walkthrough

## 13. Frontend UI Updates

-   [ ] 13.1 Update `index.blade.php` - add file upload section (unchanged)
-   [ ] 13.2 Create `preview-columns.blade.php` - show detected columns
-   [ ] 13.3 Create `column-selector.blade.php` - multi-select for key columns
-   [ ] 13.4 Update `result.blade.php` - show comparison metadata + findings
-   [ ] 13.5 Add RTL styling to all new components
-   [ ] 13.6 Add loading spinner + progress bar with time estimate

## 14. Error Handling & Edge Cases

-   [ ] 14.1 Handle: File too large (>50MB) → show error with sampling option
-   [ ] 14.2 Handle: No matching rows → show warning, suggest column review
-   [ ] 14.3 Handle: Empty key column → validate before processing
-   [ ] 14.4 Handle: Column name mismatch → suggest similar names
-   [ ] 14.5 Handle: AI API timeout → show partial results from PHP analysis
-   [ ] 14.6 Test: Malformed Excel files, mixed data types, special characters

---

## Implementation Order

1. **Phase A (Foundation)**: Tasks 1, 2.1-2.2, 5.1-5.6, 10.1-10.2
2. **Phase B (Matching)**: Tasks 3, 4, 6
3. **Phase C (UI/UX)**: Tasks 2.3-2.6, 7, 8
4. **Phase D (Optimization)**: Tasks 9, 11, 13, 14
5. **Phase E (Documentation)**: Task 12

---

**Total Estimated Effort**: 40-50 hours of development
**Suggested Team**: 1-2 developers (full-time for 1 week)
