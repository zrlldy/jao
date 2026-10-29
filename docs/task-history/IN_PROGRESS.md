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

- **Current status:** Core form-builder routes and controllers exist; data and validation gaps remain to be reviewed.
- **Existing implementation:** Private CRUD and reorder routes for templates, versions, sections, fields, options, and job assignments appear in `routes/api.php:35-74`; `FormVersionController.php:31-81` handles version creation/deletion checks.
- **Remaining work:** Audit deletion and cascade paths against submitted answers; define permitted version states and whether `validation_rules` must apply on submission; decide checkbox multi-value contract; run focused tests only with separate permission if they connect to a database. Keep core builder functionality; do not rebuild it.
- **Files involved:** `routes/api.php`, `app/Http/Controllers/FormVersionController.php`, `app/Http/Controllers/FormSectionController.php`, `app/Http/Controllers/FormFieldController.php`, `app/Http/Controllers/FormFieldOptionController.php`, `app/Http/Requests/CreateFormVersionRequest.php`, `app/Http/Requests/UpdateFormVersionRequest.php`, `app/Http/Controllers/ApplicationSubmissionController.php`.
- **Current blockers:** Product decisions for validation and checkbox values; protected source/test/database operations need their own scoped approval.
- **Last verified update:** 2026-10-29, static route/request/controller inspection by the Laravel Specialist. No runtime tests run for this ledger.

### JAO-I003 - Implement onboarding workflow

- **Current status:** Scaffold only; applicant-facing onboarding is not implemented in the inspected API.
- **Existing implementation:** Onboarding controller classes and models exist, but controller methods are empty and `routes/api.php` has no onboarding routes.
- **Remaining work:** Agree on invitation, form, authorization, persistence, and lifecycle requirements, then propose the smallest protected implementation and tests. Do not mark onboarding complete based on model/schema presence.
- **Files involved:** `app/Http/Controllers/OnboardingController.php`, `app/Http/Controllers/OnboardingInvitationController.php`, onboarding models, and future approved routes/tests.
- **Current blockers:** Behavior and security contract are not established; application changes and database operations require separate scoped approval.
- **Last verified update:** 2026-10-29, static controller and route inspection by the Laravel Specialist.
