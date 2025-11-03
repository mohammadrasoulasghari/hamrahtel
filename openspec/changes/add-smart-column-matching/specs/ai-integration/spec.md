# AI Integration Specification - Smart Column Matching

## ADDED Requirements

### Requirement: Optimized Prompt Generation with Preprocessed Data
The system SHALL generate AI prompts from PHP analysis, not raw file data.

#### Scenario: Prompt includes PHP findings
- **WHEN** AI is called for semantic analysis
- **THEN** prompt includes:
  - Structured PHP findings (schema differences, data issues)
  - Sample of matched row pairs (not all rows)
  - Summary of unmatched rows
  - User context/description
- **AND** does NOT include: Raw full file contents, schema details (AI doesn't need to analyze schema)

#### Scenario: Token-efficient prompt
- **WHEN** constructing AI request
- **THEN** prompt is optimized:
  - Max 50 matched pairs (not 5000 rows)
  - Unmatched rows as summary (not full data)
  - Structured findings (JSON) instead of prose
- **AND** reduces token usage by 70-80%
- **AND** reduces API costs significantly

### Requirement: Focused AI Analysis Request
The system SHALL request specific semantic analysis, not schema exploration.

#### Scenario: AI validates data quality
- **WHEN** AI analyzes findings
- **THEN** AI is asked to:
  - Assess data quality issues found by PHP
  - Validate if differences are expected or anomalies
  - Score severity of each discrepancy
  - Suggest fixes or explanations
- **AND** AI is NOT asked to: list columns, count rows (PHP already did this)

#### Scenario: AI generates business insights
- **WHEN** comparing preprocessed data
- **THEN** AI generates:
  - Root cause analysis for differences
  - Data quality recommendations
  - Potential data integrity issues
  - Patterns or correlations found
- **AND** response focuses on actionable insights, not raw diffs

### Requirement: Time Estimation for Processing
The system SHALL estimate total processing time before starting comparison.

#### Scenario: User sees time estimate
- **WHEN** user reviews comparison preview
- **THEN** system shows:
  - Estimated total time (e.g., "تقریباً 25 ثانیه")
  - Breakdown: schema detection (~2s), joining (~3s), analysis (~5s), AI (~15s)
  - Message in Persian: "پردازش شامل [X] مرحله است"
- **AND** estimate is based on: file size, row count, selected model

#### Scenario: Estimate accuracy
- **WHEN** estimating time
- **THEN** calculation considers:
  - File sizes (discovery time)
  - Row count (joining & analysis time)
  - Selected AI model (GPT-4 slower than faster models)
  - Complexity of join operation
- **AND** estimate is within ±20% of actual time

### Requirement: Processing Progress Feedback
The system SHALL keep user informed during long-running comparisons.

#### Scenario: Progress display during processing
- **WHEN** comparison is running
- **THEN** UI shows progress bar with:
  - Current step: "Schema Detection" → "Joining Rows" → "Analysis" → "AI Processing" → "Complete"
  - Percentage complete (0-100%)
  - Elapsed time
  - Estimated remaining time
  - Visual spinner or animation

#### Scenario: Step-by-step progress
- **WHEN** user watches processing
- **THEN** each completed phase shows:
  - ✅ Schema detection complete (2s)
  - 🔄 Joining rows... (3s of 3s)
  - ⏳ Analysis (in progress)
- **AND** user knows system is working, not hung

### Requirement: AI Response Handling for Large Datasets
The system SHALL handle AI processing gracefully even with optimized data.

#### Scenario: AI timeout with partial results
- **WHEN** AI processing times out or fails
- **THEN** system displays:
  - PHP findings (always available)
  - Message: "تحلیل AI به دلایلی موفق نشد، اما یافته‌های ساختاری در دسترس است"
  - Partial AI results if any
  - User can retry AI analysis

#### Scenario: AI response too large
- **WHEN** AI response exceeds size limits
- **THEN** system:
  - Truncates response gracefully
  - Preserves key findings
  - Adds note: "تحلیل خلاصه‌شده است"
  - Offers downloadable full analysis

---

## MODIFIED Requirements

### Requirement: OpenRouter API Communication
**Original**: Send full file data to AI for comparison.
**Modified**: Send only preprocessed findings + sample data to AI for semantic analysis.

#### Scenario: Efficient API communication
- **WHEN** calling OpenRouter API
- **THEN** request includes:
  - PHP analysis object (structured data)
  - Filtered row samples (max 50)
  - File metadata (not full files)
- **AND** reduces request size by 80%
- **AND** reduces token usage & cost

#### Scenario: Reduced timeout risk
- **WHEN** API call is made with smaller payload
- **THEN** response is faster
- **AND** timeout risk is reduced
- **AND** overall system reliability improves

### Requirement: Comparison Prompt Generation
**Original**: Build prompt from raw files with samples.
**Modified**: Build prompt from PHP analysis results with filtered samples.

#### Scenario: Structured prompt format
- **WHEN** building AI prompt
- **THEN** structure is:
  ```
  1. PHP Findings (schema, differences, issues)
  2. Sample Data (matched pairs, key values)
  3. Unmatched Summary (counts, patterns)
  4. User Context (description)
  5. Analysis Request (what AI should focus on)
  ```
- **AND** avoids repetition (PHP already analyzed schema)

#### Scenario: AI Model Selection
- **WHEN** building prompt
- **THEN** model is selected based on:
  - Complexity of comparison (simple vs complex)
  - File size (smaller → faster model OK)
  - User tier/budget (if applicable)
- **AND** adjusts expected quality accordingly

### Requirement: HTML Report Generation
**Original**: AI generates HTML report from raw data.
**Modified**: AI generates HTML report focusing on semantic insights, complementing PHP findings.

#### Scenario: Report structure
- **WHEN** generating report
- **THEN** report includes:
  - PHP Section: Schema differences, matched/unmatched summary
  - AI Section: Quality assessment, insights, recommendations
  - Combined View: Side-by-side findings
- **AND** each section clearly labeled in Persian

#### Scenario: Report styling
- **WHEN** rendering report
- **THEN** uses:
  - Color coding: Green (matches), Red (differences), Yellow (warnings)
  - RTL layout (Persian)
  - Clear section headers
  - Tailwind CSS for responsive design
  - Tables for structured data
  - Charts for summary stats (optional Phase 2)

---

## REMOVED Requirements

### Requirement: Raw Full-File Data in AI Prompts
**Reason**: Wasteful token usage, poor scaling, redundant analysis (schema handled by PHP).
**Migration**: Send only preprocessed PHP findings + key samples to AI. AI focuses on semantic analysis, not schema exploration.

### Requirement: High Token Count Per Comparison
**Reason**: Expensive, scales poorly, inefficient for large files.
**Migration**: Optimized prompts reduce tokens by 70-80%. Better cost efficiency, faster responses.

