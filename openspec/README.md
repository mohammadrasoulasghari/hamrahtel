# OpenSpec Directory Structure

This directory contains specification-driven development artifacts for the **Hamrahtel** project using OpenSpec.

## Directory Layout

```
openspec/
├── AGENTS.md                    # Instructions for AI assistants (from template)
├── project.md                   # Project context, conventions, tech stack ← START HERE
├── specs/                       # Current specifications (source of truth for what IS built)
│   ├── file-upload/
│   │   └── spec.md             # File upload and parsing requirements
│   ├── file-comparison/
│   │   └── spec.md             # Comparison workflow requirements
│   └── ai-integration/
│       └── spec.md             # OpenRouter AI integration requirements
├── changes/                     # Proposals for new features (what SHOULD change)
│   ├── [change-id]/
│   │   ├── proposal.md         # Why, what, impact
│   │   ├── tasks.md            # Implementation checklist
│   │   ├── design.md           # (Optional) Technical decisions
│   │   └── specs/              # Delta specs
│   │       └── [capability]/
│   │           └── spec.md     # ADDED/MODIFIED/REMOVED requirements
│   └── archive/                # Completed and deployed changes
└── README.md                    # This file
```

---

## 📖 Getting Started

### 1. Understand the Project
Read `/openspec/project.md` to learn:
- Project purpose and goals
- Tech stack (Laravel 11, PHP 8.3, OpenRouter API, Tailwind CSS)
- Code conventions and architecture patterns
- Testing strategy and git workflow
- Important constraints and external dependencies

### 2. Learn the Workflow
Read `/OPENSPEC_WORKFLOW.md` (in root) to understand:
- Three-stage workflow (Creating, Implementing, Archiving)
- How to create a change proposal
- Spec format and requirements
- How to work with GitHub Copilot on this project

### 3. Review Current Capabilities
Check existing specs in `/openspec/specs/`:
- **file-upload**: File upload, parsing to JSON, persistence
- **file-comparison**: Two-file comparison orchestration
- **ai-integration**: OpenRouter API communication and HTML report generation

---

## 🚀 Creating a Change Proposal

### Step 1: Plan
Decide if you need a change proposal:
- ✅ YES if: Adding features, breaking changes, architecture changes, unclear requests
- ❌ NO if: Bug fixes, typos, non-breaking dependency updates

### Step 2: Create Directory
```bash
mkdir -p openspec/changes/[change-id]/specs
```
Example: `openspec/changes/add-file-validation/specs`

### Step 3: Write Proposal
Create `openspec/changes/[change-id]/proposal.md`:
```markdown
## Why
[Problem statement or opportunity]

## What Changes
- [Feature 1]
- [Feature 2]
- Mark breaking changes with **BREAKING**

## Impact
- Affected specs: [e.g., file-upload, file-comparison]
- Affected code: [e.g., ComparisonController.php]
```

### Step 4: Write Spec Deltas
Create delta specs for each affected capability:
`openspec/changes/[change-id]/specs/[capability]/spec.md`

Use these sections:
- `## ADDED Requirements` - New capabilities
- `## MODIFIED Requirements` - Changed behavior (include full requirement)
- `## REMOVED Requirements` - Deprecated features

**Format requirements:**
```markdown
### Requirement: Name
Description of what system SHALL do.

#### Scenario: Scenario name
- **WHEN** condition
- **THEN** expected result
- **AND** additional result (if needed)
```

**Critical rules:**
- ✅ Every requirement MUST have ≥1 scenario
- ✅ Scenarios MUST use `#### Scenario:` (4 hashtags)
- ✅ Use **WHEN**, **THEN**, **AND** (bold keywords)
- ❌ Don't use `- Scenario:` or `### Scenario:` (wrong format)

### Step 5: Write Tasks
Create `openspec/changes/[change-id]/tasks.md`:
```markdown
## 1. Section Name
- [ ] 1.1 Task description
- [ ] 1.2 Another task

## 2. Another Section
- [ ] 2.1 Test task
```

### Step 6: Validate
```bash
openspec validate [change-id] --strict
```

Fix any errors and re-run until validation passes.

### Step 7: Request Approval
Share the proposal with your team and wait for approval before implementing.

---

## 🔄 Implementation Workflow

Once proposal is approved:

1. **Read Documentation**
   - proposal.md: What's being built?
   - design.md (if exists): Technical decisions?
   - tasks.md: Implementation checklist?

2. **Implement Tasks**
   - Work through tasks sequentially
   - Mark with `- [x]` as you complete each
   - Focus on one task at a time

3. **Track Progress**
   - Update tasks.md after each completion
   - Communicate blockers or design changes

4. **Confirm Completion**
   - All tasks in tasks.md are checked `[x]`
   - Code passes tests and validation
   - Proposal requirements are met

---

## 📤 Archiving Completed Changes

After deployment:

```bash
openspec archive [change-id] --yes
```

This:
- Moves `changes/[change-id]/` → `changes/archive/YYYY-MM-DD-[change-id]/`
- Updates specs in `/openspec/specs/`
- Validates the entire system

---

## 📋 Common Commands

```bash
# View active changes
openspec list

# View existing specifications
openspec list --specs

# Show details of a change
openspec show [change-id] --json

# Validate a change proposal
openspec validate [change-id] --strict

# Archive a completed change
openspec archive [change-id] --yes

# Validate entire project
openspec validate --strict
```

---

## 🎯 Key Principles

1. **Specs First**: Write specifications BEFORE implementation
2. **Requirements First**: Changes are proposed before coding
3. **Approval Gate**: No implementation without proposal approval
4. **Source of Truth**: `/openspec/specs/` reflects current system state
5. **Traceability**: Every feature is traceable from spec to code

---

## 📚 References

- **Project Context**: `/openspec/project.md`
- **Workflow Guide**: `/OPENSPEC_WORKFLOW.md`
- **OpenSpec Instructions**: `/openspec/AGENTS.md`
- **Current Specs**: `/openspec/specs/*/spec.md`

---

## 🆘 Need Help?

- **How do I create a proposal?** → See OPENSPEC_WORKFLOW.md
- **What are the project conventions?** → See /openspec/project.md
- **What specs exist?** → Run `openspec list --specs`
- **Why is my validation failing?** → Run `openspec validate --strict` for details

---

**Last Updated**: 2025-11-03
**Project**: Hamrahtel (سیستم مغایرت‌یاب)
