# AI Integration Specification

## Overview
The AI Integration capability manages all communication with the OpenRouter API for intelligent file comparison analysis. It constructs structured prompts, handles API communication, processes responses, and generates HTML reports in Persian.

---

## ADDED Requirements

### Requirement: OpenRouter API Communication
The system SHALL communicate with OpenRouter API for AI-powered analysis.

#### Scenario: API request succeeds
- **WHEN** `OpenRouterService::compareFiles()` is called with file data
- **THEN** system constructs HTTP POST request to `https://openrouter.ai/api/v1/chat/completions`
- **AND** includes Bearer token from `config('openrouter.api_key')`
- **AND** sends request with 120-second timeout
- **AND** receives JSON response with AI analysis

#### Scenario: API request fails
- **WHEN** API returns error status (4xx, 5xx) or times out
- **THEN** error is caught and logged to `storage/logs/`
- **AND** user-friendly error message is returned: "خطا در ارتباط با سرویس هوشمند"
- **AND** no incomplete result is stored

#### Scenario: API response parsing
- **WHEN** API returns successful response
- **THEN** system extracts `response.choices[0].message.content`
- **AND** validates that content is HTML-formatted string
- **AND** returns content for storage and display

### Requirement: Comparison Prompt Generation
The system SHALL generate structured prompts that guide the AI to perform thorough analysis.

#### Scenario: Prompt includes file metadata
- **WHEN** prompt is built from two parsed files
- **THEN** prompt includes:
  - Row counts for each file
  - Column names for each file
  - Sample data (first 5 rows from each file)
  - User description (if provided)

#### Scenario: Prompt requests specific analysis areas
- **WHEN** prompt is generated
- **THEN** prompt instructs AI to check:
  1. Structural differences (columns, data types)
  2. Row count differences
  3. Content/value differences
  4. Columns present in one file but not other
  5. Other notable mismatches

#### Scenario: Prompt specifies output format
- **WHEN** prompt is sent to AI
- **THEN** AI is instructed to respond with:
  - HTML formatted output
  - Tailwind CSS classes for styling
  - RTL-compatible markup for Persian display
  - Color coding (red for reduction, green for increase)

### Requirement: AI Model Configuration
The system SHALL use configurable AI models via OpenRouter.

#### Scenario: Model selection
- **WHEN** system initializes
- **THEN** model is read from `config('openrouter.model')`
- **AND** default model is `openai/gpt-4o-mini`
- **AND** can be overridden in `.env` file

#### Scenario: Temperature setting
- **WHEN** API request is made
- **THEN** temperature is set to 0.3 (deterministic)
- **AND** max_tokens is set to 4000 (supports detailed analysis)

#### Scenario: Request headers
- **WHEN** API request is constructed
- **THEN** headers include:
  - `Authorization: Bearer {api_key}`
  - `Content-Type: application/json`
  - `HTTP-Referer: {app_url}` (from config)
  - `X-Title: Hamrahtel` (for OpenRouter tracking)

### Requirement: HTML Report Generation
The system SHALL produce formatted HTML comparison reports in Persian.

#### Scenario: AI generates formatted HTML
- **WHEN** AI completes analysis
- **THEN** response contains HTML with:
  - Section headings using `<h3>` tags
  - Comparison details in `<ul>` lists or `<table>` tables
  - Color-coded content (CSS classes or inline styles)
  - Persian (RTL) text formatting

#### Scenario: Report is displayed to user
- **WHEN** user views comparison result
- **THEN** HTML is rendered in browser with:
  - `dir="rtl"` attribute for RTL directionality
  - Tailwind CSS classes applied
  - Proper spacing and typography

### Requirement: Error Logging and Monitoring
The system SHALL log all API interactions for debugging and monitoring.

#### Scenario: Successful request is logged
- **WHEN** API request completes successfully
- **THEN** log entry includes:
  - Model used
  - Input file sizes
  - Response length
  - Execution time

#### Scenario: Failed request is logged
- **WHEN** API request fails
- **THEN** log entry includes:
  - Error status code
  - Error message from API
  - Request details (to aid debugging)
  - Timestamp

---

## Acceptance Criteria

✅ API communication is reliable with proper timeout handling
✅ Errors are caught and communicated clearly to users
✅ Prompts guide AI toward comprehensive analysis
✅ Output is formatted as Persian HTML with RTL support
✅ All API calls are logged for monitoring and debugging
✅ Configuration is externalized to `.env` file
