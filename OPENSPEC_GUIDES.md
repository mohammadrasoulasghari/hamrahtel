# OpenSpec Visual Guides

## 📊 Complete Workflow Diagram

```
                        ┌─────────────────────────────────────┐
                        │   NEW REQUEST / FEATURE IDEA        │
                        └──────────────┬──────────────────────┘
                                       │
                         ┌─────────────▼──────────────┐
                         │ Is this a proposal-worthy  │
                         │ change? (features, breaking)
                         │                            │
                         └──┬─────────────────┬───────┘
                            │                 │
                        YES │                 │ NO
                            ▼                 ▼
                   ┌─────────────┐   ┌─────────────────┐
                   │  STAGE 1    │   │  FIX DIRECTLY   │
                   │  CREATE     │   │  (no proposal)  │
                   │  PROPOSAL   │   │  - Bug fix      │
                   │             │   │  - Typo         │
                   │ (This is    │   │  - Config       │
                   │  where you  │   │  - Comment      │
                   │  are now!)  │   └────────┬────────┘
                   └─────────────┘            │
                        │                     │
      ┌─────────────────┼─────────────────────┘
      │                 │
      │      ┌──────────▼────────────┐
      │      │  Create Directory:    │
      │      │  changes/[change-id]/ │
      │      │  ├─ proposal.md       │
      │      │  ├─ tasks.md          │
      │      │  ├─ design.md (opt)   │
      │      │  └─ specs/            │
      │      └──────────┬────────────┘
      │                 │
      │      ┌──────────▼──────────────┐
      │      │ Write & Validate:       │
      │      │ openspec validate --    │
      │      │ strict                  │
      │      └──────────┬──────────────┘
      │                 │
      │      ┌──────────▼─────────────┐
      │      │ WAIT FOR APPROVAL      │
      │      │ (Critical Gate!)       │
      │      │ Do NOT start Stage 2   │
      │      └──────┬────────────┬────┘
      │             │            │
      │        APPROVED          │REJECTED
      │             │            │
      │             ▼            ▼
      │      ┌──────────────────────┐
      │      │ Request changes or   │
      │      │ refine proposal      │
      │      │ Go back to writing   │
      │      └──────────────────────┘
      │                 │
      │                 │
      │      ┌──────────▼────────────┐
      │      │  STAGE 2: IMPLEMENT   │
      │      │                       │
      │      │ 1. Read proposal.md   │
      │      │ 2. Read design.md     │
      │      │ 3. Read tasks.md      │
      │      │ 4. Implement each     │
      │      │    task sequentially  │
      │      │ 5. Mark [x] as done  │
      │      │ 6. Confirm all done  │
      │      └──────────┬───────────┘
      │                 │
      │      ┌──────────▼───────────┐
      │      │ Code review & tests  │
      │      │ Validate compliance  │
      │      │ with requirements    │
      │      └──────────┬──────────┘
      │                 │
      │      ┌──────────▼──────────────┐
      │      │  STAGE 3: ARCHIVE      │
      │      │  (After deployment)    │
      │      │                        │
      │      │ openspec archive      │
      │      │ [change-id] --yes     │
      │      │                        │
      │      │ Result:               │
      │      │ changes/[id]/         │
      │      │   ↓                   │
      │      │ changes/archive/      │
      │      │ YYYY-MM-DD-[id]/      │
      │      │                        │
      │      │ specs/ updated auto   │
      │      └──────────┬────────────┘
      │                 │
      │      ┌──────────▼─────────────┐
      │      │  COMPLETE ✅           │
      │      │  Change deployed       │
      │      │  Specs are current     │
      │      └────────────────────────┘
      │
      └─────────────────────────────────────────────────────────────────┘
```

---

## 🗂️ Directory Structure

```
hamrahtel/
├── OPENSPEC_SETUP_SUMMARY.md     ← Overview (you are here)
├── OPENSPEC_WORKFLOW.md          ← Detailed workflow
├── OPENSPEC_QUICK_REF.md         ← Quick reference
├── OPENSPEC_GUIDES.md            ← Visual diagrams (this file)
│
├── openspec/
│   ├── project.md                ← Project context ✅ COMPLETE
│   ├── AGENTS.md                 ← AI instructions (reference)
│   ├── README.md                 ← Directory guide ✅ COMPLETE
│   │
│   ├── specs/                    ← SOURCE OF TRUTH
│   │   ├── file-upload/
│   │   │   └── spec.md           ← ✅ Current: File upload
│   │   ├── file-comparison/
│   │   │   └── spec.md           ← ✅ Current: Comparison workflow
│   │   └── ai-integration/
│   │       └── spec.md           ← ✅ Current: AI integration
│   │
│   └── changes/                  ← PROPOSALS
│       ├── [future-id-1]/
│       │   ├── proposal.md       ← Why? What? Impact?
│       │   ├── tasks.md          ← [ ] Task checklist
│       │   ├── design.md         ← (Optional) Technical decisions
│       │   └── specs/
│       │       ├── file-upload/spec.md
│       │       ├── file-comparison/spec.md
│       │       └── ...
│       │
│       └── archive/
│           ├── 2025-11-03-add-file-validation/
│           ├── 2025-11-04-improve-prompt/
│           └── ...
```

---

## 📋 Spec File Format Visual

```
┌─────────────────────────────────────────────────────────────┐
│ ## ADDED Requirements       ← Section header (ADDED/MODIFIED) │
│ (Or: ## MODIFIED Requirements, ## REMOVED Requirements)      │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│ ### Requirement: Feature Name     ← Requirement title        │
│ Description of what system SHALL provide.                    │
│ This is the normative requirement statement.                 │
│                                                               │
│ #### Scenario: Scenario Name      ← Scenario (4 hashtags!)  │
│ - **WHEN** condition A            ← Bold WHEN                │
│ - **THEN** expected result         ← Bold THEN               │
│ - **AND** additional result        ← Bold AND (optional)     │
│                                                               │
│ #### Scenario: Another Scenario   ← Second scenario (min 1)  │
│ - **WHEN** different condition                               │
│ - **THEN** different result                                  │
│                                                               │
├─────────────────────────────────────────────────────────────┤
│ ### Requirement: Another Feature  ← Multiple requirements OK │
│ Description...                                                │
│                                                               │
│ #### Scenario: Case 1                                        │
│ - **WHEN** ...                                               │
│ - **THEN** ...                                               │
│                                                               │
└─────────────────────────────────────────────────────────────┘

✅ VALID              ❌ INVALID
────────────────────────────────────
#### Scenario:       - Scenario:
- **WHEN**           - **WHEN**  (wrong header level)
- **THEN**           - **THEN**

#### Scenario:       ### Scenario:
(✅ Exactly 4)       (❌ Wrong level)
```

---

## 🎯 Change Decision Matrix

```
┌─────────────────────────┬──────────────┬───────────────────────┐
│ Type of Change          │ Needs        │ Why / Why Not          │
│                         │ Proposal?    │                       │
├─────────────────────────┼──────────────┼───────────────────────┤
│ New feature             │ ✅ YES       │ New capability        │
│ Breaking API change     │ ✅ YES       │ API contract changes  │
│ Database schema change  │ ✅ YES       │ Breaking change       │
│ New architecture module │ ✅ YES       │ System design change  │
│ Performance optimization│ ✅ YES       │ If behavior changes   │
│ Security pattern update │ ✅ YES       │ Affects all features  │
├─────────────────────────┼──────────────┼───────────────────────┤
│ Bug fix                 │ ❌ NO        │ Restores spec         │
│ Typo fix               │ ❌ NO        │ Non-functional        │
│ Comment update         │ ❌ NO        │ Non-functional        │
│ Formatting change      │ ❌ NO        │ Non-functional        │
│ Env config update      │ ❌ NO        │ Non-breaking          │
│ Dev dependency update  │ ❌ NO        │ Non-breaking          │
│ Test for existing code │ ❌ NO        │ Testing current spec  │
├─────────────────────────┼──────────────┼───────────────────────┤
│ Unclear / Ambiguous    │ ✅ YES       │ SAFER default         │
└─────────────────────────┴──────────────┴───────────────────────┘
```

---

## 🔄 Implementation Workflow

```
┌────────────────────────────────────────────────────────┐
│ STAGE 2: IMPLEMENTATION (After Approval)              │
└────────────────────────────────────────────────────────┘

     BEFORE CODING
     ─────────────────────────────────
     1. Read proposal.md
        ↓
        "What am I building?"
        Review: Why, What Changes, Impact
     
     2. Read design.md (if exists)
        ↓
        "What are the technical decisions?"
        Review: Architecture, trade-offs
     
     3. Read tasks.md
        ↓
        "What's the checklist?"
        See: [ ] Task 1, [ ] Task 2, ...
     
     
     DURING CODING
     ─────────────────────────────────
     For each task:
     
     ┌─ Task 1.1
     │  └─ Write code
     │     └─ Mark: [x] Task 1.1
     │
     ├─ Task 1.2
     │  └─ Write code
     │     └─ Mark: [x] Task 1.2
     │
     └─ Task 2.1
        └─ Write code
           └─ Mark: [x] Task 2.1
     
     
     AFTER CODING
     ─────────────────────────────────
     1. Confirm all tasks are [x]
     
     2. Run tests
     
     3. Validate against requirements
        "Does code implement all requirements?"
     
     4. Code review
     
     5. Merge & deploy
     
     6. Then → STAGE 3: Archive

```

---

## 📊 Project Tech Stack Overview

```
┌────────────────────────────────────────────────────────────────┐
│                   HAMRAHTEL PROJECT STACK                      │
├────────────────────────────────────────────────────────────────┤
│                                                                 │
│  FRONTEND                 BACKEND              EXTERNAL         │
│  ─────────                ──────────          ─────────         │
│  Tailwind CSS 4           Laravel 11          OpenRouter API    │
│  Vite 7                   PHP 8.3             (AI Analysis)     │
│  Blade Templates          Eloquent ORM                          │
│  Feather Icons            PSR-4 namespace                       │
│  RTL Support              maatwebsite/excel                     │
│                           SQLite DB                            │
│                                                                 │
├────────────────────────────────────────────────────────────────┤
│  PRIMARY LANGUAGE: Persian (فارسی) + English                  │
│  ARCHITECTURE: MVC (Model-View-Controller)                     │
│  FILE TYPES: Excel (xlsx, xls), CSV                            │
│  OUTPUT: HTML reports with Tailwind styling                    │
└────────────────────────────────────────────────────────────────┘
```

---

## 🚀 Command Reference Map

```
┌──────────────────────────────────────────────────────┐
│ OPENSPEC COMMAND QUICK MAP                           │
├──────────────────────────────────────────────────────┤
│                                                       │
│ LIST & EXPLORE                                        │
│ ─────────────                                        │
│ openspec list                                        │
│   → Show all active change proposals                 │
│                                                       │
│ openspec list --specs                               │
│   → Show all existing specifications                 │
│                                                       │
│ openspec show [change-id]                           │
│   → Show details of a specific change               │
│                                                       │
├──────────────────────────────────────────────────────┤
│                                                       │
│ VALIDATE & QUALITY                                   │
│ ──────────────────                                  │
│ openspec validate [change-id] --strict             │
│   → Check if proposal is correctly formatted        │
│   → Must pass before sharing proposal               │
│                                                       │
│ openspec validate --strict                         │
│   → Check entire project for issues                 │
│                                                       │
├──────────────────────────────────────────────────────┤
│                                                       │
│ ARCHIVE & FINISH                                     │
│ ────────────────                                    │
│ openspec archive [change-id] --yes                 │
│   → Move change to archive after deployment         │
│   → Updates specs/ automatically                    │
│   → Use --yes to skip confirmation                  │
│                                                       │
└──────────────────────────────────────────────────────┘
```

---

## ✨ Common Scenarios

### Scenario 1: Creating Your First Proposal

```
YOU: "I want to add file upload validation"

ME:  Step 1: mkdir -p openspec/changes/add-file-validation/specs
     Step 2: Write openspec/changes/add-file-validation/proposal.md
     Step 3: Write openspec/changes/add-file-validation/tasks.md
     Step 4: Write openspec/changes/add-file-validation/specs/file-upload/spec.md
     Step 5: openspec validate add-file-validation --strict
     Step 6: I'll review and approve
     Step 7: You start STAGE 2 implementation
```

### Scenario 2: Fixing a Bug

```
YOU: "There's a bug in file parsing"

ME:  "Is this restoring intended behavior?"

YOU: "Yes, files should parse even with empty rows"

ME:  "No proposal needed. Just fix it! This is a bug fix."
     (No proposal → Fix directly → No archive needed)
```

### Scenario 3: Modifying Existing Feature

```
YOU: "I want to update the AI prompt to be more detailed"

ME:  Step 1: This is a change to ai-integration spec
     Step 2: Create: openspec/changes/update-prompt-detail/
     Step 3: Use ## MODIFIED Requirements in spec delta
     Step 4: Copy existing requirement, edit it
     Step 5: Write proposal explaining why
     Step 6: Request approval
```

---

## 🎓 Learning Path

```
┌─ Start Here
│
├─ Read: openspec/project.md
│  (Understand project context)
│
├─ Read: OPENSPEC_WORKFLOW.md
│  (Learn the workflow)
│
├─ Read: OPENSPEC_QUICK_REF.md
│  (Quick command reference)
│
├─ Explore: openspec/specs/
│  (See existing requirements)
│
├─ Try: Create your first proposal
│  (Follow OPENSPEC_WORKFLOW.md)
│
├─ Validate: openspec validate [id] --strict
│  (Check formatting)
│
└─ Share: Request approval from team
   (Then move to Stage 2 implementation)
```

---

## 📞 Quick Troubleshooting

```
PROBLEM                          SOLUTION
─────────────────────────────────────────────────────────
"Requirement must have scenario" Add #### Scenario: with WHEN/THEN

"Change has no delta"            Create specs/[capability]/spec.md
                                 with ## ADDED/MODIFIED/REMOVED

Validation fails                 Run: openspec validate --strict
                                 (See error messages)

Don't know what to create        Read: OPENSPEC_QUICK_REF.md
                                 Decision tree: "Do I need proposal?"

Can't find a spec                Run: openspec list --specs
                                 Then: openspec show [spec-name]

Directory not found              mkdir -p openspec/changes/[id]/specs/

```

---

## ✅ Checklist: Before Sharing a Proposal

```
┌─────────────────────────────────────────────────────────┐
│ PROPOSAL READINESS CHECKLIST                            │
├─────────────────────────────────────────────────────────┤
│                                                          │
│ [ ] proposal.md exists with:                           │
│     [ ] Why section (problem/opportunity)              │
│     [ ] What Changes section (bullet list)             │
│     [ ] Impact section (specs + code)                  │
│                                                          │
│ [ ] tasks.md exists with:                             │
│     [ ] Numbered sections (1. Implementation, etc)     │
│     [ ] Unchecked [ ] items (not implemented yet)      │
│     [ ] Clear, actionable descriptions                 │
│                                                          │
│ [ ] Spec deltas created with:                         │
│     [ ] ## ADDED/MODIFIED/REMOVED sections             │
│     [ ] Requirements with full text                    │
│     [ ] Every requirement has ≥1 scenario              │
│     [ ] Scenarios use #### Scenario: format            │
│     [ ] WHEN/THEN/AND use bold **markup**              │
│                                                          │
│ [ ] Validation passes:                                 │
│     openspec validate [change-id] --strict             │
│     (No errors)                                        │
│                                                          │
│ [ ] Change ID is:                                      │
│     [ ] Kebab-case (add-file-validation)              │
│     [ ] Verb-led (add-, update-, remove-)             │
│     [ ] Unique (not used before)                       │
│                                                          │
│ ✅ READY TO SHARE!                                     │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

**Created**: November 3, 2025  
**For Project**: Hamrahtel - File Comparison System  
**Framework**: OpenSpec Specification-Driven Development
