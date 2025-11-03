# Smart Column Matching & Optimized Comparison

## Why

Currently, the system processes entire large files (5000+ rows) in memory, leading to:
- **Memory exhaustion** - PHP memory limits reached with medium-sized files
- **Inaccurate AI analysis** - AI cannot meaningfully analyze incomplete data samples
- **Poor UX** - Users must guess which columns to compare; no column discovery phase
- **Inefficient processing** - Full file data sent to API wastes tokens and time
- **No time feedback** - Users have no idea how long processing will take

The system needs intelligent column discovery, join-based row matching (like SQL joins), structured comparison in PHP, and progressive UI feedback.

## What Changes

- **ADDED**: File preview phase - scan files, detect columns, show schema to user
- **ADDED**: Smart column matching - users select key columns for joining rows
- **ADDED**: Filtered row extraction - only send relevant columns to AI, not entire files
- **ADDED**: PHP-based structural comparison - detect differences before AI (fast)
- **ADDED**: AI-powered semantic analysis - AI focuses on content quality, not schema
- **ADDED**: Time estimation - show user estimated processing time
- **ADDED**: Streaming progress updates - keep user informed during processing
- **MODIFIED**: File upload flow - add schema discovery step before comparison
- **MODIFIED**: Memory handling - process files in chunks, never load entire datasets
- **BREAKING**: API response format changes - returns comparison metadata + results

## Impact

- **Affected specs**: file-upload, file-comparison, ai-integration
- **Affected code**: ComparisonController, OpenRouterService, views (index.blade.php, result.blade.php)
- **New components**: ColumnMatchingService, ComparisonAnalyzer, ProgressTracker
- **Database**: Add columns to comparisons table for metadata
- **Frontend**: New UI components for column selection, progress display

---

## Key Technical Decisions

1. **Two-phase comparison**:
   - Phase 1 (PHP): Schema analysis + row joining + structural diff detection
   - Phase 2 (AI): Content semantic analysis + insight generation

2. **Column matching strategy**:
   - User selects "key columns" (like SQL join conditions)
   - System matches rows based on key column values
   - Displays matched rows side-by-side for human verification

3. **Memory efficiency**:
   - Stream large files in chunks
   - Keep only matched rows in memory
   - Never send entire file data to AI

4. **Time estimation**:
   - Based on file size, row count, AI model
   - Show initial estimate, update as processing progresses

---

## User Journey

1. User uploads two files
2. System analyzes schema → Shows column discovery UI
3. User selects matching strategy:
   - Option A: Join by key columns (e.g., "ln" = unique ID)
   - Option B: Compare all rows (simple full comparison)
   - Option C: Filter to specific columns only
4. System shows preview of matched rows
5. User confirms → Processing starts with progress bar
6. AI analysis runs on filtered, matched data
7. Results shown with clear diff highlighting

