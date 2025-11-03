# File Upload Specification - Smart Column Matching

## ADDED Requirements

### Requirement: Schema Discovery Without Full Load
The system SHALL detect file columns and structure without loading the entire file into memory.

#### Scenario: User uploads large Excel file (5000 rows)
- **WHEN** user uploads a .xlsx file with 5000+ rows
- **THEN** system reads only header row and estimates row count
- **AND** returns detected column names, data types, and row count estimate
- **AND** uses memory < 50 KB (not full file)

#### Scenario: System detects data types
- **WHEN** schema detection analyzes sample values
- **THEN** system identifies each column as: string, integer, decimal, date, or boolean
- **AND** stores data type information for later validation

### Requirement: Column Information API
The system SHALL provide API endpoint to preview column structure of uploaded files.

#### Scenario: Preview columns endpoint
- **WHEN** user calls `POST /api/preview-columns` with two files
- **THEN** system returns schema for both files
- **AND** response includes: `{file1: {columns: [...], types: {...}, row_count: N}, file2: {...}}`
- **AND** response time is < 3 seconds

#### Scenario: Column names in Persian
- **WHEN** Excel files have Persian column headers
- **THEN** system preserves Persian text correctly
- **AND** displays column names in selector UI

### Requirement: File Size & Row Count Limits
The system SHALL validate files before processing and warn users about limits.

#### Scenario: File exceeds size limit
- **WHEN** user uploads file > 50MB
- **THEN** system rejects upload with error message: "فایل بیش از حد مجاز است"
- **AND** suggests: "حداکثر 50 مگابایت مجاز است"

#### Scenario: File exceeds row count
- **WHEN** file contains > 5000 rows (configured limit)
- **THEN** system shows warning to user
- **AND** offers options: "پردازش تمام ردیف‌ها" or "نمونه اول 5000 ردیف"

### Requirement: Column Filtering Configuration
The system SHALL allow users to select specific columns for comparison.

#### Scenario: User selects subset of columns
- **WHEN** user chooses to compare only specific columns
- **THEN** system filters both files to those columns
- **AND** stores selected column names in database
- **AND** passes only filtered data to next phase

#### Scenario: User selects key columns for joining
- **WHEN** comparing files with common identifier column
- **THEN** user can select one or more key columns (e.g., "ln", "ID", "email")
- **AND** system uses these to match rows (like SQL JOIN)

---

## MODIFIED Requirements

### Requirement: Excel and CSV File Upload
**Original**: System SHALL accept and store Excel and CSV files.
**Modified**: System SHALL accept, store, and analyze Excel and CSV files with schema discovery.

#### Scenario: File upload with schema detection
- **WHEN** user uploads file via form
- **THEN** system stores file temporarily
- **AND** immediately analyzes schema (columns, types, count)
- **AND** does NOT fully load file to memory
- **AND** returns file ID + schema to UI

#### Scenario: Memory-efficient processing
- **WHEN** processing file with large row count
- **THEN** system processes file in chunks (configurable size)
- **AND** never loads entire dataset to memory simultaneously
- **AND** frees memory after each chunk is processed

### Requirement: File Parsing to JSON
**Original**: System SHALL convert uploaded files to JSON format.
**Modified**: System SHALL convert files to JSON with support for chunked/streamed processing.

#### Scenario: Chunked file reading
- **WHEN** converting large file to JSON
- **THEN** system reads file in chunks of N rows (default 500)
- **AND** processes each chunk independently
- **AND** combines results maintaining order

#### Scenario: Column extraction during parsing
- **WHEN** parsing file with 50 columns
- **THEN** system can extract only specified columns
- **AND** reduces memory footprint proportionally
- **AND** returns smaller JSON structure

---

## REMOVED Requirements

### Requirement: Immediate Full File Load
**Reason**: Causes memory exhaustion with large files (5000+ rows). Replaced by staged discovery process.
**Migration**: Use new schema discovery phase. Full data load only when explicitly requested after column selection.

