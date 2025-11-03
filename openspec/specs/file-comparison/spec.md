# File Comparison Specification

## Overview

The File Comparison capability orchestrates the comparison workflow: accepting two parsed files (in JSON format), initiating AI analysis, and returning structured comparison results.

---

## ADDED Requirements

### Requirement: Two-File Comparison Request

The system SHALL accept two files and optional description for comparison.

#### Scenario: User submits comparison request

-   **WHEN** user uploads File 1, File 2, and optionally enters comparison description
-   **THEN** the system receives both file references and description text
-   **AND** triggers file parsing if files are new
-   **AND** prepares data for AI comparison analysis

#### Scenario: Comparison without description

-   **WHEN** user submits comparison with only two files (no description)
-   **THEN** the system uses a default analysis approach
-   **AND** AI is instructed to compare structure, row count, and content

#### Scenario: Comparison with description

-   **WHEN** user submits comparison with description (e.g., "compare customer names and addresses")
-   **THEN** AI is instructed to focus comparison on specified areas
-   **AND** results prioritize relevant differences

### Requirement: Comparison Result Storage

The system SHALL persist comparison results for history and audit trails.

#### Scenario: Comparison result is saved

-   **WHEN** AI analysis completes successfully
-   **THEN** result is stored in `Comparison` model with:
    -   `user_id` (null if unauthenticated)
    -   `file1_name`, `file2_name` (filename references)
    -   `comparison_description` (user-provided description)
    -   `result_html` (AI-generated HTML report)
    -   `created_at` (timestamp)

#### Scenario: User views comparison history

-   **WHEN** user navigates to history/results page
-   **THEN** system retrieves all `Comparison` records
-   **AND** displays list of past comparisons with timestamps

### Requirement: Comparison Workflow

The system SHALL orchestrate the complete comparison flow from file upload to result display.

#### Scenario: Full comparison workflow

-   **WHEN** user submits two files and description
-   **THEN** system:
    1. Validates and parses both files to JSON
    2. Calls AI comparison service
    3. Receives HTML result from AI
    4. Stores result in database
    5. Renders result page with formatted HTML

#### Scenario: Error handling during comparison

-   **WHEN** file parsing or AI request fails
-   **THEN** user sees appropriate error message in Persian
-   **AND** error is logged for debugging
-   **AND** no partial record is created in database

---

## Acceptance Criteria

✅ Both files are successfully compared and stored
✅ Results are retrievable from database
✅ Comparison history is accessible to users
✅ Errors do not leave incomplete records
