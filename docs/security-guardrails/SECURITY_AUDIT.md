# JAO Security Audit

Static source review, 2026-10-28. This review inspected routes, middleware, routed controllers, request rules, models, resources, invitations, submissions, jobs, relevant tests, safe configuration, and migration source. The worktree already contained uncommitted application, test, and migration changes; findings describe that worktree snapshot. No application request, database command, test, exploit, or external scan was run. Deployment topology, credential distribution, database constraints actually applied, and runtime behavior remain unverified.

## Architecture and controls

`routes/api.php:20` places the current API behind one `jao.key` middleware and the `web` group. `JaoApiKey.php:26` rejects an invalid configured key using `hash_equals`. An invitation endpoint creates a 64-character token and stores its SHA-256 hash (`ApplicationInvitationController.php:21`). Submission hashes the URL token, checks expiration and `used_at`, locks the invitation, then writes inside a transaction (`ApplicationSubmissionController.php:39`). HMAC timestamp, body, and nonce checks exist in `VerifyHmacSignature.php:52`, but no production route refers to that middleware. Tests cover API-key behavior, isolated HMAC behavior, route names, and some resource operations; no submission-flow feature test was found. There is no active upload route in `routes/api.php`; `SubmissionFileController.php` is an unregistered scaffold.

Confirmation labels: **Confirmed** means the source directly establishes the behavior or missing check; **Potential** means impact depends on deployment, concurrency, or another caller; **Needs Verification** means source alone cannot establish whether a control is intended or effective. Severity reflects a plausible production system with multiple services or staff using the API key; actual exposure may differ.

## Critical and high findings

### SEC-01 - Critical - Confirmed: template deletion can erase submitted records

- **Path and line:** `app/Http/Controllers/FormTemplateController.php:54`; `database/migrations/2026_08_27_084606_create_form_versions_table.php:18`; `database/migrations/2026_08_28_024347_create_applications_table.php:22`; `database/migrations/2026_08_28_024400_create_application_answers_table.php:18`.
- **Description and scenario:** The routed template DELETE calls `deleteOrFail()` without checking submitted applications. The source defines cascading foreign keys through versions, applications, and answers. A key holder deleting a template with submissions can remove the application history and answers. The JobHiring and FormVersion direct-delete guards do not cover deletion through the template parent.
- **Impact:** Irrecoverable applicant record loss unless backups restore it; possible compliance and audit impact.
- **Recommended fix:** Reject deletion while dependent applications exist; preserve submitted form versions and answers. Review the cascade policy before any schema change.
- **Database/migration impact:** A controller guard can be proposed without a migration; stronger database retention may require one. Any migration or database operation needs separate approval.
- **Approval:** Explicit approval for controller/tests; separate approval for any migration or database command.

### SEC-02 - High - Confirmed: field and section deletion can erase answers

- **Path and line:** `app/Http/Controllers/FormSectionController.php:82`; `app/Http/Controllers/FormFieldController.php:56`; `database/migrations/2026_08_27_084631_create_form_fields_table.php:17`; `database/migrations/2026_08_28_024400_create_application_answers_table.php:19`.
- **Description and scenario:** The routed DELETE handlers delete a section or field without checking whether a submitted application references its fields. Database cascades can delete the stored answers. A key holder cleaning up a published form can destroy applicant responses.
- **Impact:** Silent loss of submitted answer data while application records may remain.
- **Recommended fix:** Prevent deletion of referenced fields or sections and define an immutable submitted-form policy.
- **Database/migration impact:** A guarded controller path is possible; database retention constraints would require separately approved migration work.
- **Approval:** Explicit approval for controllers/tests; separate approval for migration or database work.

### SEC-03 - High - Confirmed: one API key grants broad read and write access

- **Path and line:** `routes/api.php:20`; `app/Http/Middleware/JaoApiKey.php:26`; `app/Http/Controllers/ApplicationController.php:15`.
- **Description and scenario:** The same key gates job management, form mutation, invitation creation, and application reads. No per-service or per-user authorization is visible on those routes. A leaked key or a lower-trust integration given that key can access and mutate all exposed records, including application metadata.
- **Impact:** Broad data disclosure and modification. Whether the key is given to a browser or lower-trust client is **Needs Verification**; the shared authorization scope is confirmed.
- **Recommended fix:** Define service roles or scoped credentials, then authorize sensitive operations and records. Keep the existing key fail-closed until replacement is approved.
- **Database/migration impact:** Depends on the chosen identity store; none should be assumed.
- **Approval:** Explicit approval for auth, routes, controllers, tests, and any separate database work.

### SEC-04 - High - Confirmed: submission accepts an unrelated form version

- **Path and line:** `app/Http/Requests/CreateApplicationAnswerRequest.php:32`; `app/Http/Controllers/ApplicationSubmissionController.php:44`.
- **Description and scenario:** Validation checks that `form_version_id` exists, then the controller saves that client-supplied ID with the invitation's job. It does not require the version to be published or assigned to that job. A holder of an invitation token and API key can submit against another existing version ID.
- **Impact:** Misattributed applications and broken form provenance; any downstream reader may trust an incorrect form association.
- **Recommended fix:** Resolve the allowed published version through the invitation's job, and reject mismatched IDs inside the same write boundary.
- **Database/migration impact:** No migration is necessary for the immediate check; a cross-table invariant may warrant separate schema work.
- **Approval:** Explicit approval for submission/request/tests; separate approval if schema changes are proposed.

### SEC-05 - High - Confirmed: submission discards every answer and consumes the invitation

- **Path and line:** `app/Http/Controllers/ApplicationSubmissionController.php:48`; `app/Http/Controllers/ApplicationSubmissionController.php:60`; `app/Http/Requests/CreateApplicationAnswerRequest.php:34`.
- **Description and scenario:** The controller initializes `$answers = []`, maps that empty array, inserts no answers, marks the invitation used, and returns success. A user submitting a completed form loses every response and cannot submit again through that invitation. The request rules also leave answers optional and do not bind fields to the selected version.
- **Impact:** Silent applicant-data loss and unusable submissions.
- **Recommended fix:** Use validated answers, check each field against the authorized form and its type/required rules, and keep application, answers, and token consumption atomic. Add an end-to-end feature test before changing behavior.
- **Database/migration impact:** The immediate fix need not change schema; uniqueness and retention constraints need separate review and approval.
- **Approval:** Explicit approval for submission/request/tests and for any database-dependent test or migration.

## Medium and lower findings

### SEC-06 - Medium - Confirmed: used invitations can still open the form

- **Path and line:** `app/Http/Controllers/ApplicationFormController.php:22`; compare `app/Http/Controllers/ApplicationSubmissionController.php:39`.
- **Description and scenario:** The form GET checks token hash and expiry but not `used_at`. Someone holding a previously used link and the API key can still retrieve the invitation email and published form until expiration.
- **Impact:** Continued access to invitation-linked personal information and form metadata after one-time use.
- **Recommended fix:** Apply the same unconsumed-token check to the form GET, or explicitly document a post-submission access policy.
- **Database/migration impact:** None expected.
- **Approval:** Explicit approval for controller/tests.

### SEC-07 - Medium - Needs Verification: HMAC protection is test-only

- **Path and line:** `app/Http/Middleware/VerifyHmacSignature.php:52`; `routes/api.php:20`; `tests/Feature/Http/Middleware/VerifyHmacSignatureTest.php:11`.
- **Description and scenario:** The HMAC middleware implements timestamp and nonce checks, but route source uses only `jao.key` and `web`; the HMAC class appears on a test route only. If signed requests are an intended production control, they currently receive no route-level enforcement.
- **Impact:** Stolen API-key traffic can be replayed within the key's lifetime; the intended HMAC threat model needs confirmation.
- **Recommended fix:** Decide which service routes require signatures and attach the middleware there, or remove the unused assurance claim. Verify shared cache behavior across workers before relying on nonce replay prevention.
- **Database/migration impact:** No migration expected; cache configuration may change.
- **Approval:** Explicit approval for routes/middleware/tests and any configuration change.

### SEC-08 - Medium - Potential: single-use is not a database invariant

- **Path and line:** `app/Http/Controllers/ApplicationSubmissionController.php:39`; `database/migrations/2026_08_27_094451_create_application_invitations_table.php:19`; `database/migrations/2026_08_28_024347_create_applications_table.php:20`.
- **Description and scenario:** The transaction locks an invitation and checks `used_at`, but migration source declares neither a unique token hash nor a unique `applications.application_invitation_id`. Another write path or a backend without effective row locking could create duplicate applications for one invitation. The current production database behavior was not checked.
- **Impact:** Duplicate submissions and inconsistent single-use state.
- **Recommended fix:** Keep the transaction and add the smallest unique database constraints after checking existing data and the production driver.
- **Database/migration impact:** Requires a migration and a data assessment; neither was performed.
- **Approval:** Separate explicit approvals for database inspection, migration edit, migration execution, and related source/tests.

### SEC-09 - Medium - Potential: bearer token may persist in queue payloads

- **Path and line:** `app/Http/Controllers/ApplicationInvitationController.php:25`; `app/Jobs/SendApplicantInvitation.php:17`; `app/Notifications/ApplicationInviteNotification.php:20`.
- **Description and scenario:** The raw invitation token is passed as a public property to a queued job and then a queued notification. If the configured queue stores serialized jobs, operators or anyone with queue-payload access may recover an unexpired bearer token. The configured runtime queue backend was not inspected.
- **Impact:** Invitation takeover before token use or expiry.
- **Recommended fix:** Encrypt durable queue payloads or avoid persisting raw tokens, and restrict queue access and retention. Keep the token usable in the delivered invitation only.
- **Database/migration impact:** Depends on queue backend; no migration assumed.
- **Approval:** Explicit approval for job/notification/config/tests and separate approval if queue storage is changed.

### SEC-10 - Medium - Potential: no route-level rate limit is declared

- **Path and line:** `routes/api.php:20`; `bootstrap/app.php:17`.
- **Description and scenario:** No throttle middleware or limiter is visible on invitation creation, form access, submission, or broad API reads. A valid key holder can send many requests; deployment-edge rate limits were not inspected. The 64-character invitation token makes online guessing impractical, so the primary concern is abuse and resource exhaustion.
- **Impact:** Mail or queue flooding, repeated validation work, and API availability loss.
- **Recommended fix:** Apply targeted limits to invitation creation and expensive endpoints after confirming existing gateway limits and normal traffic.
- **Database/migration impact:** None expected; a shared limiter store may need configuration.
- **Approval:** Explicit approval for routes/config/tests.

### SEC-11 - Low - Potential: invitation resource would expose model attributes if loaded

- **Path and line:** `app/Http/Resources/ApplicationInvitationResource.php:17`; `app/Models/ApplicationInvitation.php:18`; `app/Http/Resources/ApplicationResource.php:20`.
- **Description and scenario:** The resource delegates to the model's full array, which includes the stored token hash. Current application endpoints do not eager-load the invitation, so exposure through a current route was not confirmed; a future eager load could serialize the hash and email.
- **Impact:** Unnecessary disclosure of credential-derived data and invitation PII to a broad API-key audience.
- **Recommended fix:** Use an explicit allowlist in the resource before using it in a response.
- **Database/migration impact:** None.
- **Approval:** Explicit approval for resource/tests.

## Verification gaps and non-findings

- No database schema, live data, deployment configuration, secret values, production routes, or external services were queried. Migration files describe intended schema, not proof that production has applied it.
- No dynamic request, exploit, database-dependent test, or upload test was run. The invitation/submission concurrency and CSRF behavior need a separately approved safe test plan.
- The source uses Eloquent query builders for the routed flows reviewed; no direct string-built SQL injection was established. No active upload handler is registered. These observations are limited to inspected source and are not a penetration-test result.
