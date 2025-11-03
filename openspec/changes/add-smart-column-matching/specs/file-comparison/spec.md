# File Comparison Specification - Smart Column Matching

## ADDED Requirements

### Requirement: Column Matching Strategy Selection
The system SHALL allow users to choose how to match rows between files.

#### Scenario: Join by key column
- **WHEN** user selects "Join by key column" strategy
- **THEN** user picks one or more columns that uniquely identify rows (e.g., "ln", "ID")
- **AND** system matches rows where key column values are identical
- **AND** shows matched pairs and unmatched orphans

#### Scenario: Sequential 1-to-1 comparison
- **WHEN** user selects "Compare rows sequentially"
- **THEN** system compares File1 Row1 ↔ File2 Row1, Row2 ↔ Row2, etc.
- **AND** useful for ordered datasets
- **AND** highlights row count differences

#### Scenario: Filter to specific columns only
- **WHEN** user selects "Compare specific columns only"
- **THEN** system compares only selected columns from both files
- **AND** ignores other columns
- **AND** reduces comparison scope and memory usage

### Requirement: Row Matching & Joining
The system SHALL implement join-like logic to match rows based on key columns.

#### Scenario: Rows matched by key value
- **WHEN** system joins files on key column "ln"
- **THEN** rows with matching "ln" values are paired together
- **AND** all pairs are matched correctly (like SQL INNER JOIN)
- **AND** returns: `{matched: [...], unmatched_file1: [...], unmatched_file2: [...]}`

#### Scenario: Multiple key columns
- **WHEN** user specifies multiple key columns (e.g., "company" AND "employee_id")
- **THEN** system matches rows only if ALL key columns have matching values
- **AND** correctly handles composite keys

#### Scenario: Unmatched row identification
- **WHEN** row in File1 has no match in File2
- **THEN** system identifies it as unmatched_file1
- **AND** includes count and samples in report
- **AND** highlights discrepancies clearly

### Requirement: Preview of Matched Rows
The system SHALL show user a preview before starting comparison.

#### Scenario: User reviews matched rows
- **WHEN** user confirms column selection strategy
- **THEN** system shows preview:
  - Total matched rows count
  - Total unmatched in File1
  - Total unmatched in File2
  - Sample of first 3-5 matched pairs
- **AND** user can verify join strategy is correct
- **AND** user can cancel and adjust if needed

#### Scenario: Confidence score for joins
- **WHEN** preview shows matched row pairs
- **THEN** each pair displays:
  - Row IDs from both files
  - Key column values used for matching
  - Visual indicator of row similarities

### Requirement: Comparison Metadata & Tracking
The system SHALL store detailed metadata about comparison strategy and execution.

#### Scenario: Store matching configuration
- **WHEN** comparison is saved to database
- **THEN** stores:
  - Selected key columns
  - Join strategy used
  - Column filtering applied
  - Matched/unmatched counts
  - Processing start & end times
  - Duration in seconds

#### Scenario: User can review past comparisons
- **WHEN** user views previous comparison
- **THEN** can see:
  - Which columns were matched on
  - How many rows were joined
  - Processing time taken
  - AI findings + PHP structural findings

### Requirement: PHP-Based Structural Analysis
The system SHALL perform detailed structural comparison in PHP before AI processing.

#### Scenario: Detect schema differences
- **WHEN** comparing two file structures
- **THEN** system identifies:
  - Columns present in File1 but not File2
  - Columns in File2 but not File1
  - Columns with data type mismatches
  - Column order differences
- **AND** categorizes findings clearly

#### Scenario: Analyze matched row pairs
- **WHEN** matched rows are available
- **THEN** system analyzes all matched pairs
- **AND** detects:
  - Cell value differences (with counts)
  - Null/empty pattern differences
  - Data consistency issues
  - Type conversion problems
- **AND** generates structured findings

#### Scenario: Unmatched row analysis
- **WHEN** orphan rows exist in either file
- **THEN** system summarizes unmatched rows
- **AND** shows: row IDs, key column values, row count by file
- **AND** attempts to suggest possible matches (confidence scoring)

### Requirement: Efficient AI Input with Filtered Data
The system SHALL only send relevant data to AI, not entire files.

#### Scenario: AI receives preprocessed data
- **WHEN** sending to AI for analysis
- **THEN** sends:
  - PHP structural findings (JSON - already analyzed)
  - Sample matched row pairs (first 10-20, not all)
  - Summary of unmatched rows
  - User description/context
- **AND** does NOT send: raw full files, all rows, redundant schema info

#### Scenario: AI prompt focuses on insights
- **WHEN** AI receives analysis request
- **THEN** prompt asks for:
  - Data quality assessment
  - Semantic validation of differences
  - Business insights
  - Recommendations
- **AND** does NOT ask for: "list all columns" (PHP already did this)

---

## MODIFIED Requirements

### Requirement: Two-File Comparison Request
**Original**: User submits two files and description.
**Modified**: User submits two files, selects matching strategy, and description.

#### Scenario: Comparison workflow with column selection
- **WHEN** user uploads File1 and File2
- **THEN** system shows schema discovery
- **AND** user selects matching strategy (join/sequential/filter)
- **AND** user confirms column selection
- **AND** system previews matched rows
- **AND** user initiates comparison

#### Scenario: Time estimation before comparison
- **WHEN** user is ready to compare
- **THEN** system shows:
  - Estimated total processing time (e.g., "تقریباً 25 ثانیه")
  - Breakdown per phase: discovery, joining, analysis, AI
- **AND** user sees informed estimate before committing

### Requirement: Comparison Result Storage
**Original**: Store AI result + file references.
**Modified**: Store AI result + file references + PHP analysis + metadata.

#### Scenario: Complete comparison record
- **WHEN** comparison completes
- **THEN** stores:
  - File references
  - PHP structural analysis (JSON)
  - AI semantic analysis (HTML)
  - Matching strategy & columns used
  - Matched/unmatched counts
  - Processing duration
  - Processing started_at, completed_at

---

## REMOVED Requirements

### Requirement: Simple Full-File Comparison
**Reason**: Doesn't scale to large files; doesn't handle schema discovery; no user control over matching logic.
**Migration**: Use new Smart Column Matching workflow. Users explicitly choose matching strategy, see preview, then comparison proceeds with optimized data flow.

