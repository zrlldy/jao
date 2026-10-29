# Completed Tasks

This ledger records narrow, evidence-backed outcomes. A Git date establishes when a commit was made; for uncommitted work, the completion date is unknown and the observation date is recorded instead. "Source-verified" means the implementation exists in the inspected working tree, not that it was deployed or tested in this documentation task. Earlier audit documents are historical snapshots and must be rechecked against source. Do not recreate an outcome listed here without first confirming its current behavior.

## Dated Git history

### JAO-C001 - HMAC verification middleware introduced

- **Completion date:** 2026-10-06 (Git commit date).
- **Task title:** Add request signature verification middleware.
- **Description:** Introduce middleware that checks signed private requests.
- **Files changed:** `app/Http/Middleware/VerifyHmacSignature.php`.
- **Implementation summary:** The committed middleware checks the HMAC signature, timestamp, and nonce; the current file also uses `Cache::add` to reject a reused nonce. Route attachment is tracked separately in JAO-C005.
- **Verification evidence:** `git show --format=fuller --stat c6d9207 -- app/Http/Middleware/VerifyHmacSignature.php`; current `app/Http/Middleware/VerifyHmacSignature.php:20-54`.
- **Test results:** No test execution evidence for the 2026-10-06 commit was found. Focused test source exists at `tests/Feature/Http/Middleware/VerifyHmacSignatureTest.php`; it was not run for this ledger.
- **Security considerations:** Signature comparison uses `hash_equals`; runtime replay protection depends on the configured cache store and a correctly shared secret.
- **Known limitations:** The commit alone does not prove production route use, secret configuration, or shared cache behavior.
- **Git commit or change reference:** `c6d9207`.

### JAO-C002 - Form field save/reload fix committed

- **Completion date:** 2026-10-22 (Git commit date).
- **Task title:** Correct form field response after saving a question.
- **Description:** Repair the controller and request path associated with loading a saved field's relationships.
- **Files changed:** `app/Http/Controllers/FormFieldController.php`, `app/Http/Requests/CreateFormFieldOptionRequest.php`, `app/Http/Requests/CreateFormFieldRequest.php`, `app/Http/Requests/UpdateFormFieldRequest.php`, `routes/api.php`, `tests/Feature/Http/Controllers/FormFieldControllerTest.php`.
- **Implementation summary:** The commit changes the form field response/validation path and adds focused test cases. The commit message reports the prior save-then-reload crash; this entry confirms the code change, not a production incident resolution.
- **Verification evidence:** `git show --format=fuller --stat 889fc49 -- app routes tests`; current field routes in `routes/api.php:55-72`.
- **Test results:** Test cases were added in the commit; no execution result for that commit was found or rerun here.
- **Security considerations:** Field and option relationship checks remain subject to the current authorization and data integrity review in JAO-I002.
- **Known limitations:** No deployed or runtime confirmation was available for the original crash fix.
- **Git commit or change reference:** `889fc49`.

## Current source-verified work (completion chronology unknown)

### JAO-C003 - Project governance and architecture documents exist

- **Completion date:** Not established; files observed 2026-10-29.
- **Task title:** Document JAO architecture and security boundaries.
- **Description:** Provide project context, API and business rules, caching roadmap, security policy, approval rules, and agent guidance.
- **Files changed:** Historical change set is unavailable. Current files include `docs/project/PROJECT_CONTEXT.md`, `docs/project/ARCHITECTURE.md`, `docs/project/BUSINESS_RULES.md`, `docs/project/API_BOUNDARIES.md`, `docs/project/CACHING_ROADMAP.md`, `docs/security-guardrails/SECURITY_POLICY.md`, `docs/security-guardrails/PROTECTED_FILES.md`, `docs/security-guardrails/CHANGE_APPROVAL.md`, `docs/security-guardrails/AGENT_RULES.md`, `AI_MASTER_GUIDELINES.md`, `AGENTS.md`, and `.codex/agents/laravel-specialist.toml`.
- **Implementation summary:** These documents and the project-scoped Laravel Specialist configuration are present. Their restrictions remain authoritative; several feature-status statements describe an earlier source snapshot.
- **Verification evidence:** Static file inspection and Git status on 2026-10-29; `.codex/agents/laravel-specialist.toml` is staged, while the documentation files are untracked or modified.
- **Test results:** Documentation and agent configuration were not runtime-tested in this audit; no historical test result was established.
- **Security considerations:** `PROTECTED_FILES.md` and `CHANGE_APPROVAL.md` prohibit secret access and require scoped approval for protected operations.
- **Known limitations:** Document creation dates and commits are unknown. Stale feature claims are tracked in JAO-B006.
- **Git commit or change reference:** Current working tree; no commit reference available.

### JAO-C004 - Public/private API routes are separated in source

- **Completion date:** Not established; source observed 2026-10-29.
- **Task title:** Separate public job and applicant routes from private administration.
- **Description:** Allow public job list/detail and token-scoped applicant form/submission calls; keep administration protected.
- **Files changed:** Current uncommitted change set includes `routes/api.php`, `app/Http/Controllers/JobHiringController.php`, `app/Models/JobHiring.php`, and `tests/Feature/PublicPrivateApiBoundaryTest.php`; historical full change set is unknown.
- **Implementation summary:** `routes/api.php:20-33` exposes public job GETs plus invitation form GET and submission POST; `routes/api.php:35-77` groups administration and mutations under `jao.key`, `jao.hmac`, and `web`. Public job routes call `publicIndex`/`publicShow`, not update or delete.
- **Verification evidence:** Current route definitions and `app/Models/JobHiring.php:41-47`; Laravel Specialist read-only audit on 2026-10-29.
- **Test results:** Boundary test source exists but was not executed for this documentation task; no current test pass is claimed.
- **Security considerations:** Public job scope filters visibility; private routes require both API key and HMAC in source. Deployment, HRIS signing, and route middleware at runtime remain unverified.
- **Known limitations:** Public/private deployment and external caller compatibility remain in JAO-I001.
- **Git commit or change reference:** Current uncommitted working tree; no commit reference available.

### JAO-C005 - Private HMAC routing and named throttles exist in source

- **Completion date:** Not established; source observed 2026-10-29.
- **Task title:** Attach HMAC to private API and limit sensitive routes.
- **Description:** Require signed private requests and define route-specific rate limits.
- **Files changed:** Current uncommitted change set includes `routes/api.php`, `bootstrap/app.php`, `app/Providers/AppServiceProvider.php`, and `tests/Feature/Http/Middleware/VerifyHmacSignatureTest.php`.
- **Implementation summary:** `jao.hmac` is attached to the private route group; named limits cover public jobs, invitation-form reads, applicant submissions, and invitation creation.
- **Verification evidence:** `routes/api.php:20-77`, `bootstrap/app.php:17-22`, `app/Providers/AppServiceProvider.php:25-45`, and current HMAC middleware source.
- **Test results:** Focused test source exists; no HMAC or rate-limit test was run for this documentation task.
- **Security considerations:** HMAC uses `services.jao.hmac_secret`; this audit did not read any secret value. Replay and rate-limit guarantees depend on the live cache topology.
- **Known limitations:** Runtime cache configuration, signer interoperability, and deployed enforcement are unverified; see JAO-I001 and JAO-B001.
- **Git commit or change reference:** Current uncommitted working tree; original middleware commit `c6d9207`.

### JAO-C006 - Invitation creation and public form checks exist in source

- **Completion date:** Not established; source observed 2026-10-29.
- **Task title:** Validate and pin application invitations.
- **Description:** Create invitations for visible jobs with assigned published forms, then retrieve the pinned form through a bearer token.
- **Files changed:** Current uncommitted change set includes `app/Http/Controllers/ApplicationInvitationController.php`, `app/Http/Controllers/ApplicationFormController.php`, `app/Models/ApplicationInvitation.php`, `app/Jobs/SendApplicantInvitation.php`, `app/Notifications/ApplicationInviteNotification.php`, and `tests/Feature/Http/Controllers/ApplicationInvitationControllerTest.php`. A related migration filename is present in Git status; its contents and execution were not inspected here.
- **Implementation summary:** Creation checks job/form eligibility, stores a SHA-256 token hash and pinned form version, sets expiry, and dispatches an encrypted queued job. Public form retrieval checks token hash, expiry, unused state, job visibility, form publication, and assignment.
- **Verification evidence:** Current `ApplicationInvitationController.php:17-43`, `ApplicationFormController.php:20-37`, and `SendApplicantInvitation.php:7-18`; Laravel Specialist read-only audit.
- **Test results:** A prior session reported 29 passing tests and 106 assertions across focused invitation/submission tests using isolated in-memory SQLite. That is a historical session report, not a test run or persistent-schema check for this task.
- **Security considerations:** The raw bearer token is not stored in the invitation row; the job and pinned form are rechecked when the link is used.
- **Known limitations:** Queue delivery, actual database constraints, migration completion, and deployment were not verified; see JAO-I001.
- **Git commit or change reference:** Current uncommitted working tree; no commit reference available.

### JAO-C007 - Applicant submission persists answers in source

- **Completion date:** Not established; source observed 2026-10-29.
- **Task title:** Save one-time invitation applications and answers.
- **Description:** Accept token-scoped submissions for the pinned form and consume the invitation after saving.
- **Files changed:** Current uncommitted change set includes `app/Http/Controllers/ApplicationSubmissionController.php`, `app/Http/Requests/CreateApplicationAnswerRequest.php`, and `tests/Feature/Http/Controllers/ApplicationSubmissionControllerTest.php`. A related migration filename is present in Git status; its contents and execution were not inspected here.
- **Implementation summary:** The controller locks the invitation row in a transaction, rechecks job/form eligibility, validates answer ownership and selected values, inserts the application and answer rows, then sets `used_at`.
- **Verification evidence:** Current `ApplicationSubmissionController.php:23-109`, `CreateApplicationAnswerRequest.php:25-30`, and Laravel Specialist read-only audit.
- **Test results:** The prior session reported 29 passing tests and 106 assertions for the combined focused invitation/submission run using isolated in-memory SQLite. No tests were run for this documentation task.
- **Security considerations:** Transaction and row lock guard one-time use in source; a database uniqueness safeguard and deployment state remain unverified here.
- **Known limitations:** Per-field custom validation rules and multi-value checkbox behavior need review (JAO-I002/JAO-B003); live database constraints are unverified (JAO-I001).
- **Git commit or change reference:** Current uncommitted working tree; no commit reference available.

### JAO-C008 - Task history protocol documented

- **Completion date:** 2026-10-29 (documentation verification date).
- **Task title:** Establish shared task history and progress tracking.
- **Description:** Add evidence-labeled completed, in-progress, and backlog ledgers and require all agents to maintain them.
- **Files changed:** `docs/task-history/COMPLETED_TASKS.md`, `docs/task-history/IN_PROGRESS.md`, `docs/task-history/TASK_BACKLOG.md`, `AI_MASTER_GUIDELINES.md`, `AGENTS.md`, `docs/security-guardrails/AGENT_RULES.md`.
- **Implementation summary:** New ledgers distinguish source, Git, prior-session, and runtime evidence; the master guide defines before/after task updates; agent entry points link the protocol.
- **Verification evidence:** On 2026-10-29, `rg --files docs/task-history` listed all three ledgers; `rg -n 'Task History Protocol|docs/task-history|JAO-C008'` found protocol references in `AI_MASTER_GUIDELINES.md`, `AGENTS.md`, and `docs/security-guardrails/AGENT_RULES.md`; field-count checks found 8 completed entries with 11 required lines each, 3 in-progress entries with 7 required lines each, and 7 backlog rows. `git diff --check -- AGENTS.md docs/security-guardrails/AGENT_RULES.md` passed.
- **Test results:** Application tests intentionally skipped: documentation-only scope and explicit prohibition on database-dependent tests. Static checks only.
- **Security considerations:** Application source was read only; no application source was changed, and no database, migration content, or secrets were accessed.
- **Known limitations:** The ledgers cannot establish deployed state, unknown historical completion dates, or unobserved test results.
- **Git commit or change reference:** Current uncommitted documentation changes; no commit reference available.
