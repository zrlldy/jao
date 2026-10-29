# JAO Project Context

**Documentation date:** 2026-10-28.

**Repository revision inspected:** `9cb1a64af7f6342b9a5dde13a00d5a9bf608a452` (working tree had pre-existing changes; source observations reflect those files).

**Authority:** Repository source describes current implementation; owner statements describe intended integrations. Runtime configuration and deployed behavior were not inspected. Read [AI master guidelines](../../AI_MASTER_GUIDELINES.md) and [security policy](../security-guardrails/SECURITY_POLICY.md) before acting.

## Purpose and systems

JAO means **Job Application & Onboarding**. This repository is the Laravel REST API responsible for job and form data, invitations, application submissions, validation, and security. The owner identifies MariaDB as the project database. `config/database.php:20` has an environment-dependent connection with a `sqlite` fallback and defines a MariaDB connection at line 68; the actual active connection was not verified because environment files and databases are protected.

| System | Owner-provided responsibility | Integration boundary |
| --- | --- | --- |
| JAO backend (this repository) | Business rules, forms, invitations, submissions, onboarding data, and public/private APIs | Authorizes each caller and owns persistence. |
| Next.js frontend (separate repository) | Applicant-facing jobs, dynamic application and onboarding forms, and submissions | Browser code must never hold a privileged API key or HMAC secret. Its implementation was not inspected. |
| HRIS (separate Laravel + React system) | Internal administration and Google Forms-style form builder, including job-specific sections, fields, options, and versions | HRIS consumes JAO's configured API key on private requests; its Laravel backend is the intended caller. The React browser must not hold that key or an HMAC secret. Its implementation was not inspected. |

## Private API key ownership

**Owner-confirmed:** The JAO operator sets `JAO_API_KEY` in JAO's environment configuration. JAO does not generate or issue the key. HRIS consumes it by sending the matching key in the configured request header; HRIS does not provide the authoritative key to JAO. The trusted HRIS Laravel backend is the intended sender, but its implementation was not inspected. Source maps the setting in `config/services.php:37-42` and compares it with the request header in `app/Http/Middleware/JaoApiKey.php:13-33`. No environment file or key value was inspected. HRIS currently sends the API key without HMAC headers; coordinate any future HMAC requirement with its backend before enforcing it.

## Technology and source layout

- `composer.lock` records Laravel 13.31.0; `composer.json` requires PHP `^8.3`. The local CLI reports PHP 8.4.27; deployed PHP was not checked. Sanctum, Socialite, and Pest are dependencies, but their presence does not prove an authentication flow is routed.
- `routes/api.php` holds the API resources; `bootstrap/app.php` sets API routing, stateful API middleware, and the `jao.key` alias. `app/Http/Controllers`, `Requests`, `Resources`, `Models`, `Actions`, and `Jobs` contain the current backend logic.
- `config/cache.php:18` selects `CACHE_STORE` with a `database` fallback. Redis and file stores are defined, but the active driver is unknown. `config/queue.php:16` similarly has a database queue fallback; the active queue driver is unknown.
- Pest tests are organized under `tests/Feature` and `tests/Unit`. No tests or application commands were run for this documentation.

## Feature status at this source snapshot

| Capability | Status | Source evidence and limit |
| --- | --- | --- |
| Job hiring and form administration | **Implemented in source, API-key gated** | Resource routes in `routes/api.php:20-27`; job listing currently paginates without a published-only filter (`app/Http/Controllers/JobHiringController.php:17-24`). |
| Form versions, sections, fields, options, and job assignments | **Implemented in source, API-key gated** | Nested routes in `routes/api.php:29-46`; model relationships in `app/Models/`; assignments use `syncWithoutDetaching` (`app/Http/Controllers/JobFormVersionAssignmentController.php:32-44`). |
| Application invitation creation | **Implemented in source, private/API-key gated** | Creates a hashed random token only for a published job with an assigned published form, sets expiry, and dispatches an encrypted queued job (`app/Http/Controllers/ApplicationInvitationController.php`; `app/Jobs/SendApplicantInvitation.php`). Delivery was not verified. |
| Invitation form retrieval and application submission | **Partial public/private split implemented** | Form GET is token-scoped and rate limited without the private key; submission POST remains API-key gated while its answer-persistence and ownership gaps remain (`routes/api.php`; `app/Http/Controllers/ApplicationSubmissionController.php`). |
| HMAC security | **Middleware implemented; production route use not found** | `app/Http/Middleware/VerifyHmacSignature.php:18-56` verifies a signed request; the route in `tests/Feature/Http/Middleware/VerifyHmacSignatureTest.php:10-11` is test-only. |
| Rate limiting | **Partially implemented in source** | Named limits protect public invitation-form retrieval and private invitation creation (`routes/api.php`; `app/Providers/AppServiceProvider.php`). Gateway limits and runtime rate-limiter storage are unknown. |
| Onboarding API | **Model/schema present; endpoint workflow incomplete** | Onboarding controllers contain empty methods (`app/Http/Controllers/OnboardingController.php:13-47`); no onboarding routes appear in `routes/api.php`. |

## Boundary decisions to preserve

Public applicant calls must be possible without exposing JAO's configured API key or HMAC secret to Next.js browser code. Private HRIS calls should originate from its trusted backend; service-level authorization remains planned. The current routes express only a partial public/private split; see [API boundaries](API_BOUNDARIES.md). Do not infer deployed behavior from source alone, and do not implement further intended changes without a separate approved task.
