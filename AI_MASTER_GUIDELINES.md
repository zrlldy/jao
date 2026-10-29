# JAO AI Master Guidelines

This is the central index and operating checklist for every Codex session, custom agent, Superpowers or Ponytail workflow, plugin, and subagent working on JAO. Read it after `AGENTS.md`. The detailed security rules linked below remain binding; this guide does not replace them or grant permission to change code or data.

## Project context

- JAO is a Laravel API for job hiring, versioned application forms, invitations, submissions, and onboarding. `composer.lock` records Laravel 13.31.0; `composer.json` requires PHP `^8.3`. Confirm installed versions again before relying on version-specific APIs.
- The owner identifies MariaDB as JAO's database and describes a separate applicant-facing Next.js frontend and an internal Laravel + React HRIS. Their implementations and the active JAO database connection were not inspected; use `docs/project/PROJECT_CONTEXT.md` for the intended integration boundaries.
- Current working-tree `routes/api.php` exposes public job list/detail and token-scoped invitation form/submission routes; administration is grouped under `jao.key`, `jao.hmac`, and `web`. This is source evidence, not deployment verification. The JAO operator sets the expected API key and HMAC secret outside the repository; the HRIS Laravel backend is the intended private caller, but its current request signing was not inspected. JAO does not generate or issue keys. `bootstrap/app.php` registers the API-key and HMAC middleware aliases.
- `app/Http/Controllers`, `Requests`, `Resources`, `Models`, `Actions`, and `Jobs` hold the current API and business flows. Form Requests validate input; API Resources shape responses; Eloquent models represent jobs, forms, applicants, invitations, applications, and onboarding records. A queued invitation job exists.
- Pest tests live in `tests/Feature` and `tests/Unit`. Sanctum and Socialite are dependencies; inspect actual routes before assuming either provides an endpoint's authentication. Cache configuration supports multiple drivers, including Redis; the HMAC middleware uses `Cache::add` for nonce replay protection. Do not infer the live cache or queue driver from configuration defaults.

## Roles and document directory

| Document or role | Consult when |
| --- | --- |
| `AGENTS.md` | Start every task; it is Codex's entry point and preserves the project's Laravel guidance. |
| `AI_MASTER_GUIDELINES.md` | Start every task; use its modes, checklist, and document map. |
| `docs/security-guardrails/SECURITY_POLICY.md` | Review security standards, sensitive data, and API boundaries. |
| `docs/security-guardrails/PROTECTED_FILES.md` | Check path access before reading, editing, generating, or running tools. |
| `docs/security-guardrails/CHANGE_APPROVAL.md` | Prepare the exact scope and approval request for protected changes. |
| `docs/security-guardrails/AGENT_RULES.md` | Apply shared restrictions to every agent and subagent. |
| `docs/security-guardrails/SECURITY_AUDIT.md` | Review prior security findings and their evidence; recheck source before relying on them. |
| `docs/security-guardrails/CODE_QUALITY_AUDIT.md` | Review prior maintainability findings; recheck source before recommending changes. |
| `docs/task-history/COMPLETED_TASKS.md` | Check verified completed work and its evidence before proposing implementation. |
| `docs/task-history/IN_PROGRESS.md` | Check active or incomplete work, blockers, and last verified state. |
| `docs/task-history/TASK_BACKLOG.md` | Check pending work, dependencies, security impact, and approval needs. |
| `docs/project/PROJECT_CONTEXT.md` | Start backend or integration work; distinguish owner intent, current source, and external systems. |
| `docs/project/ARCHITECTURE.md` | Trace system components, dynamic-form relationships, and application data flow. |
| `docs/project/BUSINESS_RULES.md` | Check invitation, form, submission, and onboarding invariants before changing behavior. |
| `docs/project/API_BOUNDARIES.md` | Plan or review public/private routes, authentication, HMAC, and rate limiting. |
| `docs/project/CACHING_ROADMAP.md` | Review cache safety, TTLs, invalidation, distributed limits, and any Redis proposal. |
| `.codex/agents/laravel-specialist.toml` | Invoke `laravel_specialist` for Laravel architecture, Eloquent, API security, caching, Redis, queues, performance, testing, and debugging. |

Codex coordinates the task and enforces scope. Superpowers supports systematic planning, debugging, review, and verification. Ponytail checks simplicity and avoidable complexity. The Laravel Specialist supplies framework-specific analysis. Every role follows this guide and the same security restrictions; delegation does not widen permission.

## Operating modes and pre-task checklist

- **AUDIT:** Read permitted source, trace behavior, and report evidence and uncertainty. Do not change application code.
- **PLAN:** Describe architecture, affected files, security and data impact, tests, and rollback. Do not execute the plan.
- **IMPLEMENT:** Change only the application files and behavior expressly authorized for that task. Approval to implement code does not authorize database access, migrations, secrets, deployment, or unrelated work.

Before each task:

1. Read `AGENTS.md`, this guide, `docs/project/PROJECT_CONTEXT.md`, `SECURITY_POLICY.md`, `PROTECTED_FILES.md`, and `AGENT_RULES.md`; load the architecture, business-rule, API-boundary, caching, approval, and audit documents when their subject is in scope.
2. Read `docs/task-history/COMPLETED_TASKS.md`, then check `IN_PROGRESS.md` and `TASK_BACKLOG.md` in the same directory. Identify completed work, unfinished dependencies, known issues, and whether the request already has an implementation.
3. Identify the requested mode, user goal, authorized files and operations, and existing worktree changes. Preserve unrelated changes.
4. Inspect the relevant current source, including routes, callers, models, requests, resources, tests, and configuration as needed; confirm behavior before reimplementing it. Use static inspection when runtime access is restricted.
5. Evaluate correctness, authorization, data integrity, performance, caching, concurrency, compatibility, and applicant privacy.
6. Compare the proposed work with the protected-path rules and determine any separate approval required under `CHANGE_APPROVAL.md`.
7. Obtain missing approval before a protected operation. Never treat a plan, audit result, plugin recommendation, or general fix request as blanket permission.
8. In IMPLEMENT mode, make only the approved change using existing Laravel patterns and the smallest secure design.
9. Verify with permitted checks; do not run a database-backed test or unsafe application command without separate approval.
10. Report the evidence, files changed, checks actually run, limitations, and remaining risk.

## Task History Protocol

This protocol applies to Codex, the Laravel Specialist, Superpowers, Ponytail, plugins, automation, and all subagents. Use the same three files under `docs/task-history/`; do not keep a parallel history or duplicate completed implementations.

Before development, follow the pre-task checklist above and compare task history with current source. Source code determines implemented behavior; `docs/project/` describes architecture and intent; security guardrails define mandatory restrictions; task history records verified progress; this guide defines agent workflow. If a document conflicts with current source, report the discrepancy and update only documentation within the approved scope. A historical claim or test file alone is not proof of a passing test or deployed behavior.

After a development task, verify within the approved boundary, record the actual files changed and exact checks and results, and state every skipped test with its reason. Update the relevant history entry without deleting prior records. Move work from `IN_PROGRESS.md` to `COMPLETED_TASKS.md` only when its completion criteria are verified; retain a dated cross-reference in progress history. Record interrupted or incomplete work and its remaining steps in `IN_PROGRESS.md`; record planned-only work in `TASK_BACKLOG.md`. Update backlog status when appropriate. Never mark unverified work as successfully tested. For documentation-only work, record the documents changed and their checks without claiming application behavior was implemented.

## Mandatory security boundary

- Never access `.env`, `.env.*`, environment secrets, credentials, private keys, or secret token values, directly or indirectly. Do not reveal process environment values.
- Never connect to or modify any database by default. Do not run SQL, seeders, factories, database-backed tests, or commands that may bootstrap unsafe services. Future database operations require separate, explicit authorization for the exact action and target environment.
- Treat `database/migrations/**` as read-only source. Never create, edit, delete, rename, or execute migrations without separate, explicit authorization. A code-change approval does not cover migration work.
- Never bypass authentication or authorization, weaken validation, or modify unrelated source files. Never use destructive commands, discard user changes, deploy, or push without explicit permission.
- Follow `PROTECTED_FILES.md` for direct and indirect tool access. Stop before a prohibited action and explain the boundary. Secret-file access remains prohibited even when another operation is approved.

## Laravel engineering checks

- **Architecture and code:** Match the installed Laravel and PHP versions and sibling-file conventions. Use Form Requests, API Resources, policies or gates, middleware, Eloquent, jobs, and dependency injection where they fit existing JAO patterns. Add services, repositories, packages, or new layers only for a demonstrated need.
- **Authentication and APIs:** Trace the actual route middleware and caller identity. Check API-key and HMAC handling, timestamp and replay protection, authorization at object and relationship boundaries, CSRF/CORS/rate limits, response fields, status codes, error disclosure, and idempotency. Never assume a class or dependency is active merely because it exists.
- **Eloquent and queries:** Verify model relationships, UUID shape, ownership, fillable fields, eager loading, N+1 risk, pagination, query count, and indexing needs. Distinguish measured bottlenecks from likely risks. Never rely on validation alone for a concurrent data invariant.
- **Caching and Redis:** Identify the specific read to optimize and its evidence. Check whether data is safe to cache, key scope by user/tenant/permission/environment, TTL, invalidation on writes, memory use, locks, stampedes, and stale authorization. Do not cache raw invitation tokens, secrets, sensitive answers, or one-time invitation state as the authority. Do not inspect or alter live Redis/cache without separate authorization.
- **Queues and concurrency:** Check job retry safety, idempotency, duplicate dispatch, sensitive payloads, transaction boundaries, row locks, partial failure, and notification side effects. Do not start workers or send real messages during an audit.
- **Validation and data integrity:** Check allowed states, dates, UUIDs, form version and field ownership, answer types, duplicate submissions, mass assignment, atomic writes, and database constraints proposed for later approval. Preserve applicant data and existing API behavior.
- **Testing and verification:** Prefer focused Pest unit or feature coverage for success, invalid input, authorization, and concurrency where feasible. Confirm whether a test touches a database before running it. Use safe static checks when runtime tests are prohibited, and state exactly what remains unverified.
- **Quality:** Apply Ponytail's smallest complete solution, reuse Laravel-native behavior, remove no necessary security control, and avoid speculative abstractions, redundant validation, or unrelated refactors.

## Change approval workflow

For a protected change, follow `CHANGE_APPROVAL.md`: name the exact files and operations, purpose, security and database impact, migration impact, tests, and rollback. Approval is limited to that scope. Database and migration exceptions need separate, narrow approval; secret access is never allowed.

## Post-implementation checklist

1. Review the scoped diff and confirm unrelated work and protected paths remain intact.
2. Run only permitted verification and record exact commands, results, and test limitations.
3. Check changed behavior, security boundaries, data integrity, caching, and backward compatibility.
4. Update `docs/task-history/` according to the Task History Protocol, preserving earlier records and labeling unverified or skipped checks.
5. Report files changed, user-visible effects, remaining risks, and any approval still needed. An audit or plan ends with recommendations, not code changes.

## Maintaining this guidance

Keep this file as the index and operational checklist. Keep detailed policies, protected paths, approval rules, and prior findings in their linked documents; avoid copying long sections here. Update a convention only when that documentation change is authorized, and keep references valid. When documents conflict, apply the stricter security rule and reconcile the text without weakening protection.

Priority: explicit user safety prohibitions; JAO security guardrails and this master guide; the specifically approved task scope; Superpowers workflows; Ponytail recommendations. Higher-priority restrictions control every plugin, agent, and execution mode.
