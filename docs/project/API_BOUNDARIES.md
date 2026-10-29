# JAO API Boundaries and Rate-Limiting Plan

**Documentation date:** 2026-10-28. **Source revision:** `9cb1a64af7f6342b9a5dde13a00d5a9bf608a452` plus pre-existing working-tree changes. Route classifications below describe **current source protection** separately from **owner-intended callers**. No live routes, gateway rules, or deployments were tested.

## Route inventory and caller classification

`routes/api.php` places administrative routes and application submission inside `['jao.key', 'web']`. The invitation-form GET is outside that group and has its own throttle; invitation creation remains private with a separate throttle. `app/Http/Middleware/JaoApiKey.php:13-33` checks the configured key sent in the request header. `bootstrap/app.php:19-22` registers `jao.key` and `application.invitation`; the latter is not used in the route file. No production route applies `VerifyHmacSignature`.

**Current source classification:** invitation-form GET is the only confirmed public applicant-facing API route. Application submission remains API-key gated pending its data-integrity fixes. Administrative routes remain private. The `/up` health route also requires deployment review.

| Routes in source | Current classification | Intended boundary and review |
| --- | --- | --- |
| `/api/departments`, `/employment-types`, `/locations`, `/job-hirings`, `/form-templates` resource routes | **Private/API-key gated** | HRIS administration. A separate public job listing is intended; existing job index includes drafts unless filtered (`app/Http/Controllers/JobHiringController.php:17-24`). |
| `/api/job-hirings/{job_hiring}/form-versions`, `/invitations` | **Private/API-key gated** | HRIS backend should assign versions and create invitations with scoped permissions. |
| Nested form versions, sections, fields, options; reorder routes | **Private/API-key gated** | HRIS form builder. Nested scope binding is declared for part of these routes (`routes/api.php:35-50`); shallow routes and explicit reorder routes need their own ownership review. |
| `/api/applications` and nested application answers | **Private/API-key gated** | Internal application reads; review service authorization and response exposure. |
| `GET /api/application-invitations/{token}/form` | **Public, token-scoped and rate limited in source** | The controller checks the hashed token, expiry, unused state, and assigned published form. Review response privacy and runtime behavior before deployment. |
| `POST /api/application-invitations/{token}/submissions` | **Intended public, currently API-key gated** | Applicant submission needs token-scoped authorization, duplicate/replay protection, ownership validation, and endpoint-specific throttling. |
| Separate public invitation validation, public jobs, onboarding form/submission endpoints | **Planned; no matching active route found** | Do not invent paths or treat controller methods as deployed endpoints. The unrouted `checkToken()` method was removed; form GET is the existing token check. |
| `/up` health path | **Unknown/requires deployment review** | Registered in `bootstrap/app.php:15`, outside the inspected API route group. Confirm external exposure and monitoring requirements. |

## Authentication, HMAC, and authorization status

- **Current API key and ownership:** The JAO operator sets `JAO_API_KEY` in JAO's environment; JAO does not generate or issue it. HRIS consumes the key by sending it in the configured header on private requests; it does not provide the authoritative value to JAO. Its Laravel backend is the intended sender, but that implementation was not inspected. `config/services.php:37-42` maps the setting, and `jao.key` uses `hash_equals` (`app/Http/Middleware/JaoApiKey.php:13-33`). HRIS currently sends the API key without HMAC headers. Source does not establish per-service identity or permissions; a key does not identify an applicant or authorize every record. No environment value was inspected.
- **HMAC implementation, not routed:** `app/Http/Middleware/VerifyHmacSignature.php:20-56` requires 64 hex signature, numeric timestamp, UUID nonce, configured signing secret, a +/-300-second window, and an HMAC-SHA256 over method, request URI, timestamp, nonce, and raw body. It then calls `Cache::add` with a hashed nonce and expiry based on the timestamp. Signature generation was found in a test helper (`tests/Feature/Http/Middleware/VerifyHmacSignatureTest.php:14-25`); the HRIS signer was not inspected. The test creates its own temporary route (`tests/Feature/Http/Middleware/VerifyHmacSignatureTest.php:10-11`), which is not evidence of production use.
- **Replay limits:** HMAC nonce rejection depends on an atomic cache store shared by every application instance. The active store and topology are unknown. No service identity or permission map appears in the inspected HMAC middleware; current private routes have API-key protection but no routed HMAC or explicit service-permission checks. A browser-based React or Next.js client must not be given private keys or signing secrets.
- **Invitation path:** form and submission controllers hash the URL token before lookup, but the unused invitation middleware compares the raw token with the stored hash (`app/Http/Middleware/ValidateApplicationInvitation.php:20-29`). See [business rules](BUSINESS_RULES.md) for the incomplete one-time and ownership checks.

## Endpoint-specific limits and proposals

The form-retrieval and invitation-creation limits below are implemented in source; all other budgets remain proposals. Validate them against actual traffic, accessibility needs, trusted proxy configuration, and any upstream gateway limits. Use privacy-preserving IP and invitation fingerprints; never log raw tokens. Keep the database as the final authority for one-time use.

| Endpoint class | Proposed starting limit | Rationale and lockout control |
| --- | --- | --- |
| Public published job list/detail | 60 requests/minute per client IP | Allows browsing while limiting bulk scraping; paginate and cap page size. Measure shared-NAT traffic before enforcing. |
| Invitation validation | 5 requests/minute per IP plus 5/minute per secure token fingerprint | Slows guessing and repeated failures without revealing whether a token exists. Use a separate failure budget and avoid permanent lockout of a valid applicant. |
| Authorized dynamic form retrieval | **Implemented in source:** 30 requests/minute per IP plus 15/minute per token fingerprint | Permits reloads while bounding token abuse. Do not cache personalized response data across applicants. |
| Application submission | 10 attempts/minute per IP plus 5/minute per token fingerprint; require one successful use transactionally | Limits retries and duplicate work. Preserve recoverable validation errors; tune for slow connections and legitimate retries. |
| Planned onboarding form/submission | Start with the form/submission budgets above, keyed to onboarding invitation fingerprint | Confirm actual routes and token lifecycle first; do not apply a rule to nonexistent endpoints. |
| Private HRIS reads and writes | Start with 120 reads/minute and 30 writes/minute per authenticated service; lower invitation creation as needed | Use service identity, not only shared IP. Protect costly operations while allowing internal workflows. |
| Private invitation creation | **Implemented in source:** 10 requests/minute per IP, behind `jao.key` | Bounds invitation email abuse; confirm HRIS bulk workflow and proxy behavior before deployment. |

Return HTTP 429 with `Retry-After` and a safe JSON body when a limit is reached. Combine client IP, service identity, endpoint class, and a one-way token fingerprint where appropriate. Validate trusted proxy headers so attackers cannot spoof the IP key. Audit only minimal, redacted metadata. Revisit limits after measuring burst traffic and applicant lockouts. The active rate-limiter store, reverse-proxy behavior, and gateway limits are **unknown**. See [caching roadmap](CACHING_ROADMAP.md) for multi-instance storage considerations.
