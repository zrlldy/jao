# Completed Tasks

This ledger records narrow, evidence-backed outcomes. A Git date establishes when a commit was made; for uncommitted work, the completion date is unknown and the observation date is recorded instead. "Source-verified" means the implementation exists in the inspected working tree, not that it was deployed or tested in this documentation task. Earlier audit documents are historical snapshots and must be rechecked against source. Do not recreate an outcome listed here without first confirming its current behavior.

### JAO-C009 - Redesign transactional mail templates

- **Completion date:** 2026-10-29 (local verification date).
- **Task title:** Improve HTML and plain-text notification emails.
- **Description:** Give transactional messages a clearer subject, action, fallback link, and consistent East Equator Express branding.
- **Files changed:** `resources/views/vendor/mail/html/button.blade.php`, `footer.blade.php`, `header.blade.php`, `layout.blade.php`, `message.blade.php`, `themes/default.css`; `resources/views/vendor/mail/text/header.blade.php`, `message.blade.php`; `resources/views/vendor/notifications/email.blade.php`; `public/images/application-mail-placeholder.jpg`, `mail-logo.png`; `config/services.php`; `app/Notifications/ApplicationInviteNotification.php`; `tests/Feature/Notifications/TestMailNotificationTest.php`; this task-history file and `docs/task-history/IN_PROGRESS.md`.
- **Implementation summary:** Replaced the image-dependent header and heavy banner with a text wordmark and readable subject hierarchy; simplified the action button and footer; aligned plain-text branding; replaced password-reset-only subcopy with a generic fallback URL for all action notifications.
- **Verification evidence:** Focused notification test and scoped Git diff reviewed on 2026-10-29; `git diff --check` passed.
- **Test results:** `php artisan test --compact tests/Feature/Notifications/TestMailNotificationTest.php --no-ansi` passed: 3 tests, 19 assertions. It had 2 failures before this edit. No database-backed tests, email delivery, or mail-client screenshot checks were run. A new test was not added because `tests/**` requires separate scoped approval under `PROTECTED_FILES.md`.
- **Security considerations:** No invitation, token-generation, delivery, authentication, database, or migration code changed. Fallback link is shown only when an action is present; action URLs are already carried by the original message.
- **Known limitations:** Rendering in individual email clients and real delivery remain unverified.
- **Git commit or change reference:** Current uncommitted working tree; no commit reference available.
- **Follow-up, 2026-10-29:** A user-supplied Gmail screenshot exposed the button table floating beside later paragraphs. Restored a full-width action table, adopted the supplied centered blue-and-white email layout with Poppins and a red action, and removed generic greeting/sign-off copy. The focused notification test passed again (3 tests, 19 assertions); a synthetic invitation was rendered and visually checked at 1440, 768, and 390 pixels. Real Gmail delivery and Poppins loading in Gmail remain unverified; Gmail may use the fallback font.
- **Palette follow-up, 2026-10-29:** Applied the user-specified `#800000`, `#FDFDFF`, `#292F36`, and `#CB0101` across the HTML mail theme. A synthetic invitation was visually checked at desktop and phone widths. With explicit scoped approval, updated only the previous-red assertion in `tests/Feature/Notifications/TestMailNotificationTest.php`; the focused test passed (3 tests, 19 assertions). `git diff --check` passed. No real email was sent or database test run.
- **Reference-layout follow-up, 2026-10-29:** Used the supplied email image as composition guidance only. The HTML template now has a distinct brand strip, circular action mark, centered subject, prominent action panel, expiry copy, fallback URL, and compact footer, while retaining the four approved colors and actual JAO message content. Changed `resources/views/vendor/mail/html/layout.blade.php`, `button.blade.php`, and `themes/default.css`. The focused notification test passed (3 tests, 19 assertions), the CSS contains only the four approved hex colors, and a synthetic invitation was visually reviewed at desktop and phone widths. No live email was sent; no hosted hero image was available in the repository.
- **Generated-image follow-up, 2026-10-29:** Generated a 1040px-wide, 95 KB JPEG placeholder of an invitation letter and route at `public/images/application-mail-placeholder.jpg` using the built-in image generation tool, then placed it after the introduction in `resources/views/vendor/notifications/email.blade.php` and styled it in `resources/views/vendor/mail/html/themes/default.css`. Reduced the action panel to a centered button. The focused notification test passed (3 tests, 19 assertions); synthetic HTML with the image was visually reviewed at desktop and phone widths. Real delivery and public asset URL availability were not verified.
- **Modern-layout follow-up, 2026-10-29:** After the user shared a real Gmail screenshot, replaced the heavy maroon canvas and charcoal header with an off-white editorial layout, removed the circular arrow and dark URL box, and retained the requested four-color palette and Poppins styling. Moved the generated illustration into the HTML-only mail component and embedded it as one inline MIME attachment during sending, so it no longer depends on a publicly reachable local asset URL; browser previews keep the asset fallback. The focused notification test passed (3 tests, 19 assertions); an in-memory array-transport send had one inline attachment and a CID image in HTML with no image tag in plain text; synthetic desktop and mobile previews were visually reviewed; `git diff --check` passed. No real email was sent. The invitation action URL shown in the user screenshot appears malformed and remains outside this template-only change.
- **Footer-alignment follow-up, 2026-10-29:** Centered the footer address and links in `resources/views/vendor/mail/html/themes/default.css` to match the user's supplied layout. The focused notification test passed (3 tests, 19 assertions), rendered HTML showed the footer cell's inline `text-align: center`, and `git diff --check` passed. The invitation URL remains unchanged pending the separately scoped protected-source approval required by `CHANGE_APPROVAL.md`.
- **Career-link follow-up, 2026-10-29:** With explicit approval for the three protected files, changed `config/services.php` so `services.jao.url` reads `URL_CAREER`, prefixed the supplied bare career host with HTTPS in `app/Notifications/ApplicationInviteNotification.php`, and added a dummy-token action/fallback assertion in `tests/Feature/Notifications/TestMailNotificationTest.php`. The focused test passed (4 tests, 22 assertions); Pint passed on only the three scoped PHP files, and `git diff --check` passed. No environment file, database, migration, or live email was accessed. Runtime delivery and deployment config cache remain unverified.
- **Logo-header follow-up, 2026-10-29:** Replaced the HTML mail header text with the user-supplied `https://office.eastequatorexpress.com/images/logo.svg` image URL as the direct `src`, sized it to 210px with company-name alt text, removed the obsolete localhost header link, and left a text-only company name in plain-text mail. The focused notification test passed (4 tests, 22 assertions); rendered HTML contained the logo URL and no localhost header link; `git diff --check` passed. The user confirmed the remote `src` is intentional. The external logo endpoint returned HTTP 403 during both direct and browser-user-agent fetch attempts, so recipient-visible rendering remains unverified.
- **Attached-logo follow-up, 2026-10-29:** After the user supplied `logoEEE.svg`, extracted its embedded PNG artwork without altering the logo, saved it as `public/images/mail-logo.png`, and changed the HTML header to embed that PNG as an inline MIME image during sending. Browser previews use the local asset URL. A synthetic invitation was visually reviewed at desktop and phone widths; the focused notification test passed (4 tests, 22 assertions); an in-memory array-transport send contained exactly two inline images (logo and illustration), two CID references, and no remote logo URL. No live email was sent or database accessed.
- **Logo-size follow-up, 2026-10-29:** Centered the HTML mail logo using the header cell alignment and reduced its displayed width from 210px to 120px in `resources/views/vendor/mail/html/header.blade.php` and `themes/default.css`. Rendered HTML showed the centered 120px image; desktop and phone previews were visually reviewed; the focused notification test passed (4 tests, 22 assertions); `git diff --check` passed. No live email was sent.
- **Dedicated-invitation follow-up, 2026-10-29:** Added `resources/views/mail/application-invitation.blade.php` and `application-invitation-text.blade.php` as invitation-specific HTML and plain-text views, then wired `app/Notifications/ApplicationInviteNotification.php` to select them. Removed the invitation illustration from shared `resources/views/vendor/mail/html/message.blade.php`, its shared CSS, and the now-unused notification view prop. With scoped approval, updated `tests/Feature/Notifications/TestMailNotificationTest.php` to verify the dedicated views, career URL, expiry, and two CID-embedded images in an in-memory mail send; the focused test passed (4 tests, 28 assertions). Desktop and phone synthetic previews were visually reviewed, Pint passed for the two approved PHP files, and `git diff --check` passed. No database-backed tests or live email were run.

### JAO-C010 - Add hiring-stage notification templates and senders

- **Completion date:** 2026-10-29 (local verification date).
- **Task title:** Prepare distinct emails for hiring and onboarding stages.
- **Description:** Provide callable, stage-specific notifications with branded HTML and plain-text versions for application received, interview invitation, rescheduled, cancelled, and passed, application rejected, job offer, and onboarding invitation.
- **Files changed:** Eight `app/Notifications/*Notification.php` classes named for those stages; eight HTML and eight text views in `resources/views/mail/`; shared `resources/views/mail/shell.blade.php`, `text-shell.blade.php`, `action.blade.php`, and `interview-details.blade.php`; `tests/Feature/Notifications/HiringStageNotificationsTest.php`; this task-history file and `docs/task-history/IN_PROGRESS.md`.
- **Implementation summary:** Each notification selects its own HTML/text view and accepts only the data needed by that stage. The HTML views share the existing East Equator Express palette, Poppins typography, small centered inline logo, and email-safe table structure. Offer and onboarding notifications require HTTPS action URLs supplied by their caller. No automatic send trigger was added.
- **Verification evidence:** In-memory array-transport sends rendered all eight HTML/text pairs with dummy data. A test found and corrected HTML entities appearing in plain-text job titles. The existing application invitation test remained passing.
- **Test results:** `php artisan test --compact tests/Feature/Notifications/HiringStageNotificationsTest.php tests/Feature/Notifications/TestMailNotificationTest.php` passed (14 tests, 150 assertions). Scoped Pint passed on the eight new PHP notification files and the new test; `git diff --check` passed. No database-backed tests, live email, or individual mail-client screenshots were run.
- **Security considerations:** No routes, workflow state, database, migration, secret, or live message were changed. The two action URLs are checked for HTTPS; HTML variables are Blade-escaped. The classes were created with exact-file approval under `CHANGE_APPROVAL.md`.
- **Known limitations:** HR workflow events and sending authorization are not defined here; onboarding API work remains tracked in JAO-I003. Caller-provided action links must be generated by the future authorized workflow.
- **Git commit or change reference:** Current uncommitted working tree; no commit reference available.

- **Live-send follow-up, 2026-10-29:** At the user's explicit request, reran the two focused mail test files (14 tests, 150 assertions), confirmed the configured default mail transport was SMTP without inspecting credentials, and sent one `[TEST]` message for each of the eight hiring-stage notifications to the user-provided test inbox. Each synchronous on-demand notification command returned success. Messages used sample job and interview details; offer and onboarding buttons used nonfunctional `example.test` URLs. No database, migration, secret value, or real applicant record was accessed. SMTP acceptance was observed; recipient inbox delivery and client rendering were not verified.

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
