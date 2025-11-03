# OpenSpec Workflow Guide for Hamrahtel

This document explains how to work with me (GitHub Copilot) using the OpenSpec specification-driven development methodology on the **Hamrahtel** project.

---

## 📋 Quick Reference

| Task | Command/Action |
|------|---|
| View all active change proposals | `openspec list` |
| View all existing specifications | `openspec list --specs` |
| View details of a change | `openspec show [change-id] --json` |
| Create a new change | Follow the 3-stage workflow below |
| Validate your proposal | `openspec validate [change-id] --strict` |
| Archive a completed change | `openspec archive [change-id] --yes` |

---

## 🎯 Three-Stage Workflow

### **Stage 1: Creating Changes** ← You are here for planning

**When to create a change proposal:**
- ✅ Adding new features or capabilities
- ✅ Making breaking changes (API, schema, behavior)
- ✅ Changing architecture or patterns
- ✅ Optimizing performance (when it changes behavior)
- ✅ Updating security patterns
- ✅ When you're uncertain (safer to propose first)

**When NOT to create a proposal:**
- ❌ Bug fixes (restoring intended behavior)
- ❌ Typo/formatting/comment fixes
- ❌ Non-breaking dependency updates
- ❌ Configuration changes

**Workflow:**

1. **Review Current State**
   ```bash
   openspec list              # See active changes
   openspec list --specs      # See existing capabilities
   ```

2. **Choose a Unique Change ID** (kebab-case, verb-led)
   - Good: `add-file-validation`, `update-ai-prompt-structure`, `refactor-service-layer`
   - Bad: `new-feature`, `fix`, `update` (too vague)

3. **Create Directory Structure**
   ```
   openspec/changes/[change-id]/
   ├── proposal.md      # Why, what, impact
   ├── tasks.md         # Implementation checklist
   ├── design.md        # (Optional) Technical decisions
   └── specs/           # Delta specifications
       └── [capability]/
           └── spec.md  # ADDED/MODIFIED/REMOVED requirements
   ```

4. **Write Proposal** (`proposal.md`)
   ```markdown
   ## Why
   [1-2 sentences on problem/opportunity]

   ## What Changes
   - [Bullet list of features/changes]
   - Mark breaking changes with **BREAKING**

   ## Impact
   - Affected specs: [capabilities like "File Comparison", "API Integration"]
   - Affected code: [key files]
   ```

5. **Write Spec Deltas** (`specs/[capability]/spec.md`)
   - Use `## ADDED Requirements` for new capabilities
   - Use `## MODIFIED Requirements` for changed behavior
   - Use `## REMOVED Requirements` for deprecated features
   - **Every requirement MUST have at least one scenario with `#### Scenario:` format**
   
   Example:
   ```markdown
   ## ADDED Requirements
   
   ### Requirement: File Validation
   The system SHALL validate uploaded files before processing.
   
   #### Scenario: Valid file uploaded
   - **WHEN** user uploads a .xlsx file with valid format
   - **THEN** file is accepted and processed
   
   #### Scenario: Invalid file uploaded
   - **WHEN** user uploads a .txt file
   - **THEN** error message is shown and file rejected
   ```

6. **Write Tasks** (`tasks.md`)
   ```markdown
   ## 1. Implementation
   - [ ] 1.1 Task description
   - [ ] 1.2 Another task
   
   ## 2. Testing
   - [ ] 2.1 Write tests for...
   ```

7. **Validate**
   ```bash
   openspec validate [change-id] --strict
   ```
   Fix any errors and re-run until validation passes.

8. **Request Approval** ← **Do NOT implement until approved**
   Share the proposal and wait for feedback.

---

### **Stage 2: Implementing Changes** ← Implementation phase

Once proposal is approved:

1. **Read Documentation**
   - [ ] Read `proposal.md` - understand what's being built
   - [ ] Read `design.md` (if it exists) - review technical decisions
   - [ ] Read `tasks.md` - get the implementation checklist

2. **Implement Tasks**
   - Complete tasks sequentially from `tasks.md`
   - Mark each with `- [x]` as you finish
   - Focus on one task at a time

3. **Track Progress**
   - Update `tasks.md` after each completed task
   - Keep me informed of blockers or changes

4. **Confirm Completion**
   - Ensure every task in `tasks.md` is checked off
   - Verify code passes validation tests
   - Review `proposal.md` to confirm all changes are implemented

---

### **Stage 3: Archiving Changes** ← After deployment

After the feature is deployed:

1. **Archive the Change**
   ```bash
   openspec archive [change-id] --yes
   ```
   This moves `changes/[change-id]/` → `changes/archive/YYYY-MM-DD-[change-id]/`

2. **Verify**
   ```bash
   openspec validate --strict
   ```
   Ensure validation still passes.

---

## 📚 Project Context Reference

Your project uses:
- **Framework**: Laravel 11 with PHP 8.3
- **Frontend**: Tailwind CSS 4, Vite
- **External API**: OpenRouter for AI analysis
- **Key File Types**: Excel/CSV parsing, Persian (RTL) UI, HTML comparison reports
- **Database Model**: Comparisons table for history tracking

See `/openspec/project.md` for complete conventions, architecture patterns, and constraints.

---

## 🚀 Example: Creating a Change Proposal

**Scenario:** You want to add file upload validation.

### Step 1: Create Directory
```bash
mkdir -p openspec/changes/add-file-validation/specs/file-upload
```

### Step 2: Write `proposal.md`
```markdown
## Why
Currently, users can upload any file type. This creates API errors when non-Excel/CSV files are processed, resulting in poor user experience. We need to validate file types and sizes before processing.

## What Changes
- Add file type validation (only .xlsx, .xls, .csv allowed)
- Add file size limit (max 50MB per file)
- Show user-friendly error messages for invalid uploads
- Prevent API calls for invalid files (cost savings)

## Impact
- Affected specs: File Upload, Error Handling
- Affected code: ComparisonController, Blade templates
```

### Step 3: Write Spec Delta
`openspec/changes/add-file-validation/specs/file-upload/spec.md`:
```markdown
## ADDED Requirements

### Requirement: File Type Validation
The system SHALL accept only Excel (.xlsx, .xls) and CSV (.csv) file types.

#### Scenario: Valid Excel file
- **WHEN** user uploads a .xlsx file
- **THEN** file is accepted

#### Scenario: Invalid file type
- **WHEN** user uploads a .txt file
- **THEN** error message "فقط فایل‌های Excel و CSV پذیرفته می‌شوند" is displayed
- **AND** file is rejected

### Requirement: File Size Limit
The system SHALL reject files larger than 50MB.

#### Scenario: File within size limit
- **WHEN** user uploads a 10MB file
- **THEN** file is accepted

#### Scenario: File exceeds limit
- **WHEN** user uploads a 100MB file
- **THEN** error message "حجم فایل بیش از حد مجاز است" is displayed
```

### Step 4: Write `tasks.md`
```markdown
## 1. Validation Implementation
- [ ] 1.1 Add mime type validation in ComparisonController
- [ ] 1.2 Add file size validation
- [ ] 1.3 Create custom validation error messages in Persian

## 2. Frontend Updates
- [ ] 2.1 Update upload form UI to show accepted formats
- [ ] 2.2 Add client-side file type check for fast feedback
- [ ] 2.3 Show error modal for invalid files

## 3. Testing
- [ ] 3.1 Write test for valid Excel upload
- [ ] 3.2 Write test for invalid file type rejection
- [ ] 3.3 Write test for file size rejection
- [ ] 3.4 Test error messages display correctly

## 4. Documentation
- [ ] 4.1 Update README.md with file requirements
```

### Step 5: Validate
```bash
openspec validate add-file-validation --strict
```

### Step 6: Request Approval
"I've created a change proposal for file upload validation. Please review `openspec/changes/add-file-validation/` and let me know if this aligns with your goals."

---

## ✅ Before Starting Any Task

Always check:
- [ ] Read `openspec/project.md` for conventions
- [ ] Run `openspec list` to see active changes
- [ ] Run `openspec list --specs` to see existing capabilities
- [ ] Check for conflicts with other pending changes
- [ ] Understand the affected specs and code files

---

## 🔍 Spec Format Rules

### Requirements Format
```markdown
### Requirement: Name of Requirement
Description of what the system SHALL provide.

#### Scenario: Scenario Name
- **WHEN** condition
- **THEN** expected outcome
- **AND** additional outcome (if any)
```

### Critical Rules
✅ Use `#### Scenario:` (4 hashtags exactly)
✅ Every requirement MUST have ≥1 scenario
✅ Use **WHEN**, **THEN**, **AND** bold keywords
✅ Use SHA, MODIFIED, REMOVED for delta operations
❌ Don't use `- Scenario:` or `### Scenario:` (wrong format)
❌ Don't write requirements without scenarios

---

## 🛠️ Common Tasks

### I want to add a new feature
1. Create proposal following Stage 1 above
2. I'll help scaffold the files
3. Write spec deltas describing new requirements
4. Request approval
5. Once approved, implement following Stage 2

### I want to modify existing behavior
1. Locate the capability spec in `openspec/specs/[capability]/spec.md`
2. Use `## MODIFIED Requirements` in your delta
3. Copy the entire existing requirement and paste it in the delta
4. Edit to reflect new behavior
5. Include at least one scenario

### I want to remove a feature
1. Use `## REMOVED Requirements` in delta
2. Include reason and migration plan
3. Run validation to ensure consistency

### I want to understand a spec
1. Read `openspec/specs/[capability]/spec.md`
2. Focus on the requirements and scenarios
3. Ask me for clarification on any part

---

## 📞 Getting Help

- **For OpenSpec questions**: Review `openspec/AGENTS.md`
- **For project conventions**: Check `openspec/project.md`
- **For existing specs**: Run `openspec list --specs` and `openspec show [spec]`
- **For validation errors**: Run `openspec validate --strict` to see detailed errors

---

## Summary

1. **Planning**: Create proposal in Stage 1 with complete specs → Request approval
2. **Building**: Implement following Stage 2 after approval → Update tasks.md
3. **Finishing**: Archive after deployment in Stage 3 → Specs become source of truth

**Key Principle**: Specifications come FIRST, code comes SECOND. This ensures clear requirements before writing code.

Happy building! 🚀
