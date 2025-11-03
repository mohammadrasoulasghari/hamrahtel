# Project Context

## Purpose

**Hamrahtel** (سیستم مغایرت‌یاب) is an intelligent file comparison system designed to analyze and compare Excel (XLSX, XLS) and CSV files using AI-powered insights. The system automatically converts files to JSON, uses OpenRouter's AI APIs to detect structural differences, data mismatches, and anomalies, then presents findings in a beautiful Persian-language HTML report with Tailwind CSS styling.

**Primary Goals:**

-   Streamline file comparison workflows for Persian-speaking users
-   Leverage AI to identify non-obvious differences in datasets
-   Provide intuitive, accessible comparison UI with RTL support
-   Log all comparisons for audit and historical tracking

## Tech Stack

### Backend

-   **Laravel 11** - Web framework and MVC architecture
-   **PHP 8.3+** - Server-side runtime
-   **maatwebsite/excel** - Excel file parsing (XLSX, XLS)
-   **Guzzle HTTP** - HTTP client for external API calls
-   **SQLite** - Default database (configurable)

### Frontend

-   **Tailwind CSS 4.0** - Utility-first CSS framework with RTL support
-   **Vite 7** - Frontend build tool
-   **Feather Icons** - SVG icon library
-   **Axios** - JavaScript HTTP client
-   **Blade Templates** - Laravel's templating engine with Persian support

### External Services

-   **OpenRouter API** - AI model access (supports GPT-4o, Claude, etc.)
-   API Endpoint: `https://openrouter.ai/api/v1/chat/completions`

### DevTools

-   **Laravel Pint** - PHP code formatting
-   **PHPUnit 11.5** - Testing framework
-   **Composer** - PHP dependency manager
-   **npm/Node.js** - Frontend build automation
-   **concurrently** - Run dev tasks in parallel

## Project Conventions

### Code Style

**PHP (Backend)**

-   PSR-4 autoloading in `App\` namespace
-   Service layer pattern: `App\Services\*` for business logic
-   Controllers in `App\Http\Controllers\` - keep endpoints thin, delegate to services
-   Models in `App\Models\` - leverage Eloquent ORM
-   Use Laravel facades for config, logging, HTTP
-   Document Persian features (RTL, text directionality) in comments
-   Naming: camelCase for methods/properties, PascalCase for classes

**JavaScript/Frontend**

-   ES6+ modules via Vite
-   Use `axios` for HTTP calls
-   Write vanilla JS or minimal frameworks to keep bundle small
-   Respect RTL layout: use CSS logical properties (`start`, `end` instead of `left`, `right`)
-   Add `dir="rtl"` to HTML root when rendering Persian UI

**Blade Templates**

-   Store in `resources/views/`
-   Use Blade directives for control flow
-   RTL attributes in root layout
-   Localize Persian text inline (no i18n framework yet)

### Architecture Patterns

**Service Layer:**

-   `OpenRouterService` handles all AI communication:
    -   Receives parsed file data (JSON arrays)
    -   Constructs structured comparison prompts
    -   Manages API timeouts and error recovery
    -   Logs all API calls and responses

**File Processing Pipeline:**

1. User uploads files → stored in `storage/app/`
2. Controller invokes file reader (maatwebsite/excel)
3. Excel/CSV converted to JSON (array of associative arrays)
4. JSON passed to `OpenRouterService::compareFiles()`
5. AI generates structured HTML analysis
6. Result stored in `Comparison` model and displayed

**Database Models:**

-   `User` - Future auth (scaffolded)
-   `Comparison` - Stores comparison history (user_id, file1_name, file2_name, result_html, created_at)

### Testing Strategy

-   **PHPUnit** for feature and unit tests
-   Test location: `tests/Feature/` and `tests/Unit/`
-   Run: `composer test` (clears config and runs tests)
-   Coverage focus: Service layer (OpenRouterService), Controller endpoints, File parsing
-   Mocking: Mock OpenRouter responses to avoid API quota usage in tests

### Git Workflow

-   **Primary Branch**: `develop` (current active branch)
-   **Feature Branches**: `feature/description` for new capabilities
-   **Hotfix Branches**: `hotfix/description` for production fixes
-   **Commits**: Conventional commits encouraged (feat:, fix:, refactor:, docs:, test:)
-   **PRs**: Describe changes, link related issues, run tests before merging

## Domain Context

### File Comparison Domain

-   Files are tabular data (rows × columns)
-   Key comparison areas: schema (columns), row count, data types, values
-   AI prompt emphasizes structural and content differences
-   Persian reporting: Results formatted as Persian HTML with RTL directionality
-   Supported formats: XLSX (Excel 2007+), XLS (Excel 97-2003), CSV (any encoding)

### OpenRouter Integration

-   API Key stored in `.env` as `OPENROUTER_API_KEY`
-   Model configurable: `OPENROUTER_MODEL=openai/gpt-4o-mini` (default)
-   Temperature: 0.3 (deterministic output favored)
-   Max tokens: 4000 (allows detailed analysis)
-   Timeout: 120 seconds (accounts for large files)
-   Headers required: `Authorization`, `Content-Type`, `HTTP-Referer`, `X-Title`

### User Experience

-   Single-page flow: upload two files → describe comparison goal → view results
-   No user authentication yet (open platform)
-   Results stored for history/audit trails
-   Multilingual UI: Persian as default, English fallback possible

## Important Constraints

### Technical

-   **File Size Limits**: Dependent on PHP `upload_max_filesize` and server memory
-   **API Rate Limits**: OpenRouter has quota-based billing; large-scale use requires monitoring
-   **Storage Quota**: Results stored in DB; old records should be pruned regularly
-   **Timeout**: 120s API timeout; very large files (>10K rows) may exceed limits

### Business/Domain

-   **Data Privacy**: Comparison data sent to OpenRouter (third-party); inform users
-   **Cost Model**: OpenRouter charges per token; costs scale with file size and complexity
-   **Accuracy**: AI results are suggestions, not authoritative; users must validate
-   **Language**: Primary language is Persian; English support secondary

### Operational

-   Requires valid OpenRouter API key; system fails gracefully without it
-   Database must be migrated before first run (`php artisan migrate`)
-   Symbolic link for storage must be created (`php artisan storage:link`)
-   No background queue workers required (sync processing in early stages)

## External Dependencies

### Services

-   **OpenRouter API** (`https://openrouter.ai/api/v1`)
    -   Purpose: AI-powered file analysis
    -   Auth: Bearer token in `Authorization` header
    -   Timeout tolerance: Required for large data payloads
    -   Fallback: None (graceful error message shown)

### Libraries

-   **maatwebsite/excel**: Excel file parsing with stream support
-   **Laravel Framework**: Web server, routing, ORM, config management
-   **Tailwind CSS**: Utility-first styling with RTL plugin support
-   **Vite**: Modern bundler for frontend assets

### Infrastructure

-   **Web Server**: Laravel Artisan dev server or nginx/Apache (production)
-   **Database**: SQLite (default; MySQL/PostgreSQL compatible via config)
-   **Cache/Sessions**: File-based (configurable to Redis)
-   **Logging**: File-based in `storage/logs/` (PSR-3 compliant)

```

```
