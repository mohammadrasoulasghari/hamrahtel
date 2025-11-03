# File Upload Specification

## Overview
The File Upload capability handles user file uploads and processing of Excel and CSV files for comparison analysis. Files are stored, validated, and converted to JSON format for downstream processing.

---

## ADDED Requirements

### Requirement: Excel and CSV File Upload
The system SHALL accept and store Excel (.xlsx, .xls) and CSV (.csv) files uploaded by users.

#### Scenario: User uploads valid XLSX file
- **WHEN** user selects a .xlsx file and submits the upload form
- **THEN** file is received and stored in `storage/app/uploads/`
- **AND** file is registered in the database with filename and upload timestamp

#### Scenario: User uploads valid CSV file
- **WHEN** user selects a .csv file and submits the upload form
- **THEN** file is received and stored in `storage/app/uploads/`
- **AND** file is registered in the database with filename and upload timestamp

### Requirement: File Parsing to JSON
The system SHALL convert uploaded Excel and CSV files to JSON (array of associative arrays) for processing.

#### Scenario: Excel file is parsed
- **WHEN** a valid .xlsx file is uploaded
- **THEN** the system uses maatwebsite/excel to read the file
- **AND** converts each row to an associative array keyed by column headers
- **AND** returns JSON array structure

#### Scenario: CSV file is parsed
- **WHEN** a valid .csv file is uploaded
- **THEN** the system reads the CSV with header row as keys
- **AND** converts each subsequent row to an associative array
- **AND** returns JSON array structure

#### Scenario: File with no data
- **WHEN** a file with headers but no data rows is uploaded
- **THEN** an empty JSON array is returned
- **AND** processing continues (AI comparison handles empty state)

### Requirement: File Persistence
The system SHALL store uploaded files for later access and auditing.

#### Scenario: Uploaded file is retrievable
- **WHEN** a file is uploaded successfully
- **THEN** the file is stored in persistent storage (`storage/app/`)
- **AND** a reference record is created in the `Comparison` model
- **AND** the file can be retrieved via its filename reference

#### Scenario: Symbolic link for public access
- **WHEN** the system is initialized
- **THEN** `storage/app/` is linked to `public/storage/`
- **AND** files can be downloaded by authenticated requests

---

## Acceptance Criteria

✅ Files are stored with unique names to prevent conflicts
✅ JSON output preserves all data from source files
✅ Empty files are handled gracefully
✅ File metadata (upload time, filename) is logged
