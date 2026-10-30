# In-Progress Tasks

These entries track incomplete outcomes, including source work awaiting integration evidence. "Last verified" is the date of the stated observation, not a claimed completion date. Do not rebuild existing implementation solely because runtime verification is pending. Preserve entries when resolving them; add a dated resolution and link to the completed entry.

### JAO-I001 - Verify API and invitation release readiness

- **Current status:** Source implementation exists; integration and deployed behavior are unverified.
- **Existing implementation:** Public/private route split, private API key plus HMAC, named throttles, pinned invitations, public form retrieval, and transactional answer persistence are present in current source (JAO-C004 through JAO-C007).
- **Remaining work:** Confirm the approved target database has the required invitation form-version and application uniqueness constraints; verify HRIS sends compatible HMAC requests; check shared cache behavior for nonce replay and limits; run permitted integration tests and confirm queue delivery in the intended environment. Do not repeat already implemented route/controller work without a failing check.
- **Files involved:** `routes/api.php`, `bootstrap/app.php`, `app/Http/Middleware/VerifyHmacSignature.php`, `app/Providers/AppServiceProvider.php`, `app/Http/Controllers/ApplicationInvitationController.php`, `app/Http/Controllers/ApplicationFormController.php`, `app/Http/Controllers/ApplicationSubmissionController.php`, related focused tests, and the related migration named in Git status.
- **Current blockers:** This documentation task forbids database access, migration reads/execution, database-dependent tests, secret inspection, and application execution. A previous user message showed `migrate:fresh` beginning on disposable local data, but did not include final completion output; no schema or deployment claim can be made from it.
- **Last verified update:** 2026-10-29, static source/Git inspection and Laravel Specialist audit. Prior focused test result is historical only (JAO-C006/JAO-C007).

### JAO-I002 - Finish dynamic form integrity review

- **Current status:** Approved builder and private-upload source is staged; 56 focused tests pass against temporary in-memory SQLite. No persistent database has been migrated or checked.
- **Existing implementation:** Draft-only builder mutations lock the parent version and reject edits after applications. Publication requires a populated form and server-managed date; deletion guards preserve assigned versions, invitations, and descendant saved answers. Reorders are bounded and applicant form retrieval follows `sort_order`. `file` questions accept one PDF, JPEG, or PNG per answer in token-scoped multipart submissions, with 10 MB per-file and 50 MB aggregate limits. Files use generated names on the private local disk; the protected application-answer route downloads them as attachments without exposing storage paths.
- **Remaining work:** Apply both new migrations to each intended persistent database under separate environment-specific approval before deploying this code. Decide the checkbox multi-value and stored `validation_rules` contract. Configure web server and PHP multipart size limits for the intended upload allowance; determine whether malware scanning is required before release.
- **Files involved:** `app/Http/Controllers/{ApplicationForm,ApplicationSubmission,ApplicationAnswer,FormTemplate,FormVersion,FormSection,FormField,FormFieldOption}Controller.php`; `app/Http/Requests/{Create,Update}FormVersionRequest.php`, `{Create,Update}FormFieldRequest.php`, `{Create,Update}FormFieldOptionRequest.php`, `CreateApplicationAnswerRequest.php`; `app/Models/ApplicationAnswer.php`; `app/Http/Resources/ApplicationAnswerResources.php`; `routes/api.php`; `database/migrations/2026_10_30_000001_scope_form_version_number_uniqueness.php`, `2026_10_30_000002_add_file_metadata_to_application_answers.php`; the eight matching focused controller test files.
- **Current blockers:** No persistent migration command or database environment was approved. Do not deploy source before both migrations: submissions now write the new metadata columns. The migration rollback guards refuse to restore global version uniqueness after duplicate numbers or drop file metadata while uploaded files exist; either case needs a data-preserving forward fix.
- **Last verified update:** 2026-10-30, `DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test --compact` on the eight focused controller test files passed (56 tests, 230 assertions), including both rollback guards. `vendor/bin/pint --format agent --` ran on the 28 approved PHP files; `php -l` passed on all 28, with the four final edits rechecked; `git diff --check` passed. A real-content upload test rejected PHP content disguised with a `.pdf` name. No persistent database, migration command, live upload, malware scanner, or deployment check ran.

### JAO-I003 - Implement onboarding workflow

- **Current status:** Scaffold only; applicant-facing onboarding is not implemented in the inspected API.
- **Existing implementation:** Onboarding controller classes and models exist, but controller methods are empty and `routes/api.php` has no onboarding routes.
- **Remaining work:** Agree on invitation, form, authorization, persistence, and lifecycle requirements, then propose the smallest protected implementation and tests. Do not mark onboarding complete based on model/schema presence.
- **Files involved:** `app/Http/Controllers/OnboardingController.php`, `app/Http/Controllers/OnboardingInvitationController.php`, onboarding models, and future approved routes/tests.
- **Current blockers:** Behavior and security contract are not established; application changes and database operations require separate scoped approval.
- **Last verified update:** 2026-10-29, static controller and route inspection by the Laravel Specialist.

### JAO-I004 - Make seeded job hirings visible on the public API

- **Current status:** Seed source and regression test updated; existing database rows and runtime response remain unverified.
- **Existing implementation:** `JobHiringFactory.php` now defaults to `draft`, `JobHiringSeed.php` explicitly seeds `published` jobs, and `DatabaseSeederTest.php` checks that seeding produces publicly visible jobs.
- **Remaining work:** Run the focused database-backed test in an explicitly approved test environment. Inspect the intended existing database and update only approved job rows if they should be published; code changes alone do not alter stored statuses.
- **Files involved:** `database/factories/JobHiringFactory.php`, `database/seeders/JobHiringSeed.php`, `tests/Feature/DatabaseSeederTest.php`.
- **Current blockers:** JAO guardrails require separate approval for database-backed tests and for any read or write against the existing database.
- **Last verified update:** 2026-10-29, PHP syntax checks and scoped Git diff check passed; no database access or runtime test was performed.

### JAO-I005 - Use the career host for invitation links

- **Current status:** Resolved in the working tree on 2026-10-29; see JAO-C009 career-link follow-up. Live delivery and deployed config remain unverified.
- **Existing implementation:** `services.jao.url` now reads `URL_CAREER`; `ApplicationInviteNotification::toMail()` builds an HTTPS `/application/form/{token}` URL used by the button and fallback copy.
- **Remaining work:** Deployment verification only: refresh cached configuration if used and inspect a newly sent invitation in the intended mail client.
- **Files involved:** `config/services.php`, `app/Notifications/ApplicationInviteNotification.php`, and `tests/Feature/Notifications/TestMailNotificationTest.php`.
- **Current blockers:** No local implementation blocker; deployment and real delivery are outside this task's approved scope. Environment files and live tokens remain off limits.
- **Last verified update:** 2026-10-29, scoped approval received; focused test passed (4 tests, 22 assertions), Pint passed on the three PHP files, and `git diff --check` passed. No database accessed.

### JAO-I006 - Verify the mail logo image is recipient-accessible

- **Current status:** Resolved in the working tree on 2026-10-29; see JAO-C009 attached-logo follow-up. Live Gmail rendering remains unverified.
- **Existing implementation:** `resources/views/vendor/mail/html/header.blade.php` embeds the locally stored `public/images/mail-logo.png` from the user-supplied SVG; plain-text mail retains the company name.
- **Remaining work:** Confirm appearance in a newly sent email when live delivery testing is authorized.
- **Files involved:** `resources/views/vendor/mail/html/header.blade.php`, `resources/views/vendor/mail/html/message.blade.php`, and `public/images/mail-logo.png`.
- **Current blockers:** No local implementation blocker. Live delivery is outside this task's verification scope.
- **Last verified update:** 2026-10-29, logo centered at 120px in rendered HTML and desktop/mobile previews; focused test passed (4 tests, 22 assertions). Earlier in-memory send had two inline images and no remote logo URL. No live email was sent.

### JAO-I007 - Add hiring-stage email templates and callable notifications

- **Current status:** Resolved in the working tree on 2026-10-29; see JAO-C010. The eight notification classes and their HTML/plain-text views are ready for callers. Automatic HR workflow triggers remain a separate integration task.
- **Existing implementation:** Templates cover application received, interview invitation, interview rescheduled, interview cancelled, interview passed, application rejected, job offer, and onboarding invitation. The existing application invitation remains wired to its dedicated view. Shared action and interview-detail partials use the approved palette and Poppins with email-safe table markup.
- **Remaining work:** Define the source events and authorization for sending each stage from the HR workflow. Confirm recipient inbox delivery and client rendering of the test messages.
- **Files involved:** `resources/views/mail/*.blade.php`, `app/Notifications/{ApplicationReceived,InterviewInvitation,InterviewRescheduled,InterviewCancelled,InterviewPassed,ApplicationRejected,JobOffer,OnboardingInvitation}Notification.php`, and `tests/Feature/Notifications/HiringStageNotificationsTest.php`.
- **Current blockers:** No blocker for callable notifications. HR integration behavior is not yet specified; no database, migration, secret, or live email action was performed.
- **Last verified update:** 2026-10-29, focused mail tests passed (14 tests, 150 assertions); the configured SMTP transport returned success for eight `[TEST]` on-demand messages sent at the user's request. Inbox delivery remains unverified; no database or secret values were accessed.
