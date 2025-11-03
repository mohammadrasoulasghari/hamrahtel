# Design: Smart Column Matching & Optimized Comparison

## Context

Current system loads entire files into memory and sends all data to AI, causing:
- Memory exhaustion (5000+ rows fails)
- Inefficient token usage (paying for schema info AI doesn't need)
- Poor UX (users must guess column relationships)
- Inaccurate results (AI can't analyze incomplete samples)

New system separates concerns: PHP handles structure, AI handles semantics.

---

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                  FILE UPLOAD (User Inputs)                  │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│           PHASE 1: SCHEMA DISCOVERY (Lightweight)           │
│  ─────────────────────────────────────────────────────────  │
│  • ColumnDetectionService::detectColumns()                  │
│  • Extract column names, types, sample values               │
│  • Estimate row count (stream header only)                  │
│  • NO FULL FILE LOAD                                        │
│  • Memory: ~10-50 KB                                        │
│  • Time: ~1-2 seconds                                       │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│        PHASE 1.5: COLUMN SELECTOR UI (User Interaction)     │
│  ─────────────────────────────────────────────────────────  │
│  • Show detected columns for both files                     │
│  • User selects strategy:                                   │
│    ├─ Option A: Join by key column (e.g., "ln" or "ID")   │
│    ├─ Option B: Compare all rows 1:1                       │
│    └─ Option C: Filter to specific columns                 │
│  • Preview: Show first matched rows                         │
│  • Allow user to adjust selection                           │
└──────────────┬──────────────────────────────────────────────┘
               │ User confirms column selection
               ▼
┌─────────────────────────────────────────────────────────────┐
│       PHASE 2: INTELLIGENT ROW JOINING (PHP - Fast)         │
│  ─────────────────────────────────────────────────────────  │
│  • RowJoiningService::joinByKeyColumns()                    │
│  • Stream both files in chunks                              │
│  • Match rows based on key columns (like SQL JOIN)          │
│  • Categorize:                                              │
│    ├─ Matched pairs (rows that join)                        │
│    ├─ Unmatched in file1 (orphans)                          │
│    └─ Unmatched in file2 (orphans)                          │
│  • Memory: Chunk size only (500 rows = ~1 MB)              │
│  • Time: ~1-2 seconds (per 1000 rows)                      │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│      PHASE 3: STRUCTURAL ANALYSIS (PHP - Detailed)          │
│  ─────────────────────────────────────────────────────────  │
│  • ComparisonAnalyzer::analyzeStructure()                   │
│  • Detect differences:                                      │
│    ├─ Column additions/removals                             │
│    ├─ Data type mismatches                                  │
│    ├─ Null/empty pattern differences                        │
│    └─ Row count discrepancies                               │
│  • Analyze matched pairs for value differences              │
│  • Generate structured findings report                      │
│  • Memory: Depends on matched row count                     │
│  • Time: ~1-3 seconds (per 1000 rows)                      │
└──────────────┬──────────────────────────────────────────────┘
               │ PHP findings prepared
               ▼
┌─────────────────────────────────────────────────────────────┐
│    PHASE 4: AI SEMANTIC ANALYSIS (OpenRouter - Insights)    │
│  ─────────────────────────────────────────────────────────  │
│  • Send to AI:                                              │
│    ├─ PHP structural findings (structured data)             │
│    ├─ Sample of matched rows (not all)                      │
│    ├─ Unmatched rows summary                                │
│    └─ User description/context                             │
│  • NO schema data (AI already knows structure)              │
│  • AI focuses on:                                           │
│    ├─ Data quality assessment                               │
│    ├─ Content validation                                    │
│    └─ Business insights                                    │
│  • Generate HTML report with findings                       │
│  • Memory: API handles it                                   │
│  • Time: ~5-30 seconds (depends on model)                  │
└──────────────┬──────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────────┐
│              RESULT: Complete Comparison Report              │
│  ─────────────────────────────────────────────────────────  │
│  • PHP findings: Schema, matching, structural issues        │
│  • AI findings: Quality insights, semantic analysis         │
│  • HTML report: Professional, RTL, color-coded             │
└─────────────────────────────────────────────────────────────┘
```

---

## Key Components

### 1. ColumnDetectionService

```php
// Detects file schema without loading entire file
class ColumnDetectionService {
    public function detectColumns($filePath) : array
    // Returns: ['columns' => [...], 'row_count' => X, 'data_types' => [...]]
    
    public function getSampleRows($filePath, $limit = 5) : array
    // Returns: First N rows for preview
    
    public function inferDataType($samples) : string
    // Returns: 'string' | 'integer' | 'decimal' | 'date' | 'boolean'
}
```

### 2. RowJoiningService

```php
// Implements join-like row matching logic
class RowJoiningService {
    public function joinByKeyColumns($data1, $data2, $keyColumns) : array
    // Returns: {
    //   'matched' => [...],        // Paired rows from both files
    //   'unmatched_file1' => [...],
    //   'unmatched_file2' => [...]
    // }
    
    public function extractColumns($data, $columnNames) : array
    // Returns: Filtered data with only specified columns
    
    public function matchRows($file1Row, $file2Data, $keyColumns) : ?array
    // Returns: Matching row from file2, or null
}
```

### 3. ComparisonAnalyzer

```php
// Analyzes structural and content differences
class ComparisonAnalyzer {
    public function analyzeStructure($data1, $data2) : array
    // Returns: {
    //   'schema_diffs' => [...],
    //   'row_count_diff' => X,
    //   'type_mismatches' => [...],
    //   'missing_patterns' => [...]
    // }
    
    public function analyzeMatchedRows($pairs) : array
    // Returns: Row-level difference details
    
    public function generateReport($analysis) : string
    // Returns: Categorized, structured findings
}
```

### 4. TimeEstimator

```php
// Estimates processing time
class TimeEstimator {
    public function estimate($fileSize, $rowCount, $aiModel) : int
    // Base calculation:
    // - Schema detection: 2 seconds
    // - Joining: 0.5s per 1000 rows
    // - Analysis: 1s per 1000 rows
    // - AI: 10-30s depending on model
    // Returns: Total estimated seconds
}
```

---

## Data Flow Improvements

### Before (Current - Problematic)

```
[File 1] ──┐
           │─→ Load entire file to JSON ──→ Send all to AI ──→ Result
[File 2] ──┘         (5000+ rows)      (Inefficient, slow, memory issues)
```

### After (Optimized)

```
[File 1] ──┐
           │─→ Schema detect ──→ User selects columns ──→ Join rows ──→ PHP analysis ──→ Send only filtered data to AI ──→ Result
[File 2] ──┘   (10 KB)        (UI interaction)      (1 MB)        (findings)        (efficient, fast, clear)
```

---

## Database Schema Changes

Add to `comparisons` table:

```sql
ALTER TABLE comparisons ADD COLUMN file1_columns JSON;
ALTER TABLE comparisons ADD COLUMN file2_columns JSON;
ALTER TABLE comparisons ADD COLUMN selected_key_columns JSON;
ALTER TABLE comparisons ADD COLUMN row_join_strategy VARCHAR(50); -- 'join' | 'sequential' | 'filtered'
ALTER TABLE comparisons ADD COLUMN matched_count INT DEFAULT 0;
ALTER TABLE comparisons ADD COLUMN unmatched_file1_count INT DEFAULT 0;
ALTER TABLE comparisons ADD COLUMN unmatched_file2_count INT DEFAULT 0;
ALTER TABLE comparisons ADD COLUMN php_analysis_result LONGTEXT;
ALTER TABLE comparisons ADD COLUMN processing_started_at TIMESTAMP NULL;
ALTER TABLE comparisons ADD COLUMN processing_completed_at TIMESTAMP NULL;
ALTER TABLE comparisons ADD COLUMN processing_duration_seconds INT;
```

---

## API Endpoints

### Discovery Phase

```
POST /api/preview-columns
Input: { file1: File, file2: File }
Output: { 
  file1: { columns: [...], row_count: N, types: {...} },
  file2: { columns: [...], row_count: N, types: {...} }
}
Time: ~2 seconds
```

### Validation Phase

```
POST /api/validate-matching
Input: { file1_id: X, file2_id: Y, key_columns: [...] }
Output: {
  matched_count: N,
  unmatched_file1: N,
  unmatched_file2: N,
  sample_pairs: [...first 5 matches]
}
Time: ~1-2 seconds
```

### Time Estimation

```
GET /api/estimate-time?file_size=X&row_count=Y&model=Z
Output: {
  estimated_seconds: 25,
  breakdown: {
    schema_detection: 2,
    joining: 3,
    analysis: 5,
    ai_processing: 15
  },
  message: "تقریباً 25 ثانیه"
}
```

---

## User Experience Flow

1. **Upload**: User uploads two files (unchanged)
2. **Preview**: System detects columns, shows schema
3. **Select Strategy**:
   - "Join by key column" → user picks column(s)
   - "Compare all rows" → system matches 1:1
   - "Filter columns" → user picks columns to compare
4. **Preview Results**: Show matched row count + sample pairs
5. **Confirm**: User confirms before processing
6. **Progress**: Show progress bar with time estimate
7. **Results**: Show PHP findings + AI insights

---

## Configuration

```php
// config/comparison.php
return [
    'max_rows_per_file' => env('COMPARISON_MAX_ROWS', 5000),
    'chunk_size' => env('COMPARISON_CHUNK_SIZE', 500),
    'enable_ai_analysis' => env('COMPARISON_ENABLE_AI', true),
    'ai_model' => env('COMPARISON_AI_MODEL', 'openai/gpt-4o-mini'),
    'timeout' => env('COMPARISON_TIMEOUT', 120),
    'max_file_size_mb' => env('COMPARISON_MAX_FILE_SIZE_MB', 50),
];
```

---

## Performance Targets

| Operation | Time | Memory |
|-----------|------|--------|
| Schema detection | 1-2s | ~20 KB |
| Row joining (5000 rows) | 2-3s | 1-2 MB |
| PHP analysis | 1-3s | ~5 MB |
| AI processing | 10-30s | API |
| **Total** | **15-40s** | **~10 MB** |

---

## Error Handling

| Scenario | Handling |
|----------|----------|
| File > 50MB | Show error, suggest sampling |
| No matching rows | Warn user, suggest review column selection |
| Column mismatch | Auto-suggest similar names |
| AI timeout | Show partial PHP results |
| Memory exceeded | Stop, show partial results |
| Invalid key column | Validate before joining, show error |

---

## Future Enhancements (Phase 2)

- [ ] Real-time progress via WebSocket/SSE
- [ ] Batch processing (compare 10+ files)
- [ ] Custom comparison rules (user-defined logic)
- [ ] Report generation (PDF export)
- [ ] Scheduled comparisons (recurring)
- [ ] Diff visualization (interactive highlight)

