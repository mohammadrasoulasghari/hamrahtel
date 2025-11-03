# OpenSpec Quick Reference Card

**Last Updated**: November 3, 2025  
**Project**: Hamrahtel (سیستم مغایرت‌یاب) - File Comparison System

---

## ⚡ 30-Second Overview

**OpenSpec** = Specification-driven development workflow
- 📋 **Stage 1**: Create proposal + specs → Request approval
- 🔨 **Stage 2**: Implement tasks → Check them off  
- 📦 **Stage 3**: Archive after deployment

**Principle**: Specs come FIRST, code comes SECOND

---

## 🎯 Decision Tree: Do I Need a Proposal?

```
┌─ New feature or capability?
│  └─→ YES: Create proposal
│
├─ Breaking change (API, schema, behavior)?
│  └─→ YES: Create proposal
│
├─ Bug fix (restoring spec behavior)?
│  └─→ NO: Fix directly
│
├─ Typo/formatting/comment?
│  └─→ NO: Fix directly
│
└─ Unclear what to do?
   └─→ YES: Create proposal (safer)
```

---

## 📋 Creating a Change Proposal (5 Steps)

### 1. **Pick Change ID** (kebab-case, verb-led)
```
✅ add-file-validation
✅ update-ai-prompt-structure
❌ new-feature
❌ update
```

### 2. **Create Directory Structure**
```
mkdir -p openspec/changes/[change-id]/specs
```

### 3. **Write `proposal.md`**
```markdown
## Why
[1-2 sentences on problem]

## What Changes
- Feature 1
- Feature 2 (**BREAKING** if applicable)

## Impact
- Affected specs: [e.g., file-upload, ai-integration]
- Affected code: [e.g., ComparisonController.php]
```

### 4. **Write Spec Deltas** (`specs/[capability]/spec.md`)
```markdown
## ADDED Requirements

### Requirement: Feature Name
System SHALL provide [capability].

#### Scenario: Success case
- **WHEN** condition
- **THEN** result
```

### 5. **Write `tasks.md`**
```markdown
## 1. Implementation
- [ ] 1.1 Task description
- [ ] 1.2 Another task
```

---

## 🔍 Spec Format Rules

### ✅ CORRECT Format
```markdown
### Requirement: Name
Description of what system SHALL do.

#### Scenario: Scenario name
- **WHEN** condition
- **THEN** result
- **AND** extra result (optional)
```

### ❌ WRONG Formats
```markdown
- Scenario: Name           ❌ (use #### not -)
### Scenario: Name         ❌ (use #### not ###)
No scenarios at all        ❌ (every requirement needs ≥1)
```

### Critical Rules
- ✅ Every requirement MUST have ≥1 scenario
- ✅ Scenarios MUST use `#### Scenario:` (4 hashtags)
- ✅ Use **WHEN**, **THEN**, **AND** (bold keywords)
- ✅ Use "system SHALL" (normative language)

---

## 🚀 Three-Stage Workflow

### Stage 1: Creating (You are here)
```bash
1. Create openspec/changes/[id]/ with proposal.md, tasks.md, specs/
2. Run openspec validate [id] --strict
3. Request approval
4. ⏸️  WAIT for approval before moving to Stage 2
```

### Stage 2: Implementing (After approval)
```bash
1. Read proposal.md → Understand what's being built
2. Read design.md (if exists) → Review decisions
3. Read tasks.md → Get implementation checklist
4. Implement each task → Mark as [x] when done
5. Confirm all tasks completed
```

### Stage 3: Archiving (After deployment)
```bash
openspec archive [change-id] --yes
# Moves to: changes/archive/YYYY-MM-DD-[change-id]/
# Updates specs automatically
```

---

## 🔧 Essential Commands

```bash
# View active change proposals
openspec list

# View existing specifications
openspec list --specs

# Show details of a change
openspec show [change-id]

# Validate a change (before sharing)
openspec validate [change-id] --strict

# Archive a completed change
openspec archive [change-id] --yes

# Validate entire project
openspec validate --strict
```

---

## 📚 Project Tech Stack

| Component | Technology |
|-----------|-----------|
| Backend | Laravel 11, PHP 8.3 |
| Frontend | Tailwind CSS 4, Vite 7 |
| File Processing | maatwebsite/excel |
| AI Integration | OpenRouter API |
| Database | SQLite (default) |
| Language | Persian (RTL) + English |

**Key Convention**: 
- Namespace: `App\` for backend
- Services for business logic: `App\Services\*`
- Controllers should be thin: delegate to services

---

## 📂 Current Specifications

| Spec | Location | Purpose |
|------|----------|---------|
| File Upload | `/openspec/specs/file-upload/spec.md` | Excel/CSV parsing to JSON |
| File Comparison | `/openspec/specs/file-comparison/spec.md` | Workflow orchestration |
| AI Integration | `/openspec/specs/ai-integration/spec.md` | OpenRouter API communication |

---

## 📖 Documentation Map

| Document | Purpose | When to Read |
|----------|---------|---|
| `/openspec/project.md` | Project context & conventions | Before any task |
| `/OPENSPEC_WORKFLOW.md` | Detailed workflow guide | Creating a proposal |
| `/openspec/README.md` | Directory structure | Understanding layout |
| `/openspec/specs/*/spec.md` | Current requirements | Understanding existing features |

---

## ❓ Quick FAQs

**Q: Should I create a proposal for this change?**
A: If uncertain, YES. Creating proposals is the safe default. If it's a bug fix, typo, or config change, you can skip it.

**Q: What if my proposal validation fails?**
A: Run `openspec validate [id] --strict` to see detailed errors. Common issues:
- Missing `#### Scenario:` format
- Requirements without scenarios
- Malformed delta operations

**Q: Can I modify a requirement?**
A: Use `## MODIFIED Requirements` in spec delta. Copy the ENTIRE existing requirement from the spec, then edit it.

**Q: What's the difference between ADDED and MODIFIED?**
- **ADDED**: New capability that can stand alone
- **MODIFIED**: Changes behavior of existing capability (must include full requirement)

**Q: When can I start implementing?**
A: ONLY after proposal is approved. Never skip the approval gate.

**Q: How do I know if tasks.md is complete?**
A: All items should be marked `- [x]` (checked). Implementation should match all proposal requirements.

---

## 🎯 Example: Your First Change

**Goal**: Add file upload validation

### Step 1: Decide
✅ This is a NEW feature → Need proposal

### Step 2: Create Directory
```bash
mkdir -p openspec/changes/add-file-validation/specs/file-upload
```

### Step 3: proposal.md
```markdown
## Why
Currently, users can upload any file type, causing API errors. We need validation.

## What Changes
- Add file type validation (.xlsx, .xls, .csv only)
- Add file size limit (50MB max)
- Show Persian error messages

## Impact
- Affected specs: file-upload
- Affected code: ComparisonController.php
```

### Step 4: specs/file-upload/spec.md
```markdown
## ADDED Requirements

### Requirement: File Type Validation
System SHALL accept only Excel and CSV files.

#### Scenario: Valid file
- **WHEN** user uploads .xlsx file
- **THEN** file is accepted

#### Scenario: Invalid file
- **WHEN** user uploads .txt file
- **THEN** error shown: "فقط فایل‌های Excel و CSV"
```

### Step 5: tasks.md
```markdown
## 1. Validation
- [ ] 1.1 Add mime type validation
- [ ] 1.2 Add file size check
- [ ] 1.3 Persian error messages

## 2. Testing
- [ ] 2.1 Test valid uploads
- [ ] 2.2 Test invalid types
```

### Step 6: Validate & Share
```bash
openspec validate add-file-validation --strict
# Share proposal with team → Wait for approval
```

---

## 🚦 Status Indicators

- 📋 **Proposed**: In `openspec/changes/[id]/`
- ✅ **Approved**: Ready for Stage 2 implementation
- 🔨 **Implementing**: Tasks being completed
- 📦 **Done**: Moved to `changes/archive/` after deployment

---

## 🆘 Troubleshooting

| Problem | Solution |
|---------|----------|
| "Requirement must have scenario" | Add `#### Scenario: Name` with WHEN/THEN |
| "Change must have at least one delta" | Create `specs/[capability]/spec.md` with operations |
| Validation fails | Run `openspec validate --strict` for details |
| Directory not found | Use `mkdir -p openspec/changes/[id]/specs/` |

---

## 💡 Best Practices

1. **Specs First**: Write proposal + specs BEFORE any code
2. **Clear Change IDs**: Use verb-led, kebab-case names
3. **Complete Scenarios**: Every requirement needs ≥1 scenario
4. **Approval First**: Never implement without approval
5. **Update Tasks**: Mark tasks as you complete them
6. **Validate Often**: Run validation before sharing
7. **Archive Always**: Archive changes after deployment

---

## 🔗 Important Links

- **Project Context**: `/openspec/project.md`
- **Workflow Guide**: `/OPENSPEC_WORKFLOW.md`
- **Current Specs**: `/openspec/specs/`
- **This Card**: `/OPENSPEC_QUICK_REF.md`

---

**Remember**: Specifications come FIRST, code comes SECOND. 

Questions? Check `/OPENSPEC_WORKFLOW.md` or `/openspec/README.md`

Good luck! 🚀
