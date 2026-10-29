# JAO Caching and Redis Roadmap

**Documentation date:** 2026-10-28. **Source revision:** `9cb1a64af7f6342b9a5dde13a00d5a9bf608a452` plus pre-existing working-tree changes. No environment file, live cache, Redis instance, or database was inspected. Read [API boundaries](API_BOUNDARIES.md) before designing rate limits.

## Current source evidence

- `config/cache.php:18` uses `CACHE_STORE` with a `database` fallback. It defines database, file, Redis, and other stores (`config/cache.php:35-108`). **The active store is unknown** because the environment value is protected; a configured Redis option is not proof Redis is running.
- The only application cache call found in the inspected `app/**` source is `Cache::add` in `app/Http/Middleware/VerifyHmacSignature.php:52`. Its key is `jao:hmac:` plus SHA-256 of the UUID nonce. TTL is `timestamp + 301 - now` seconds; within the accepted five-minute skew this is approximately 1 to 601 seconds. Expiry is the invalidation mechanism. HMAC middleware is not attached to production routes in `routes/api.php`, so this cache path is not currently established as production traffic.
- `routes/api.php` now applies named throttles to public invitation-form GET and private invitation creation; `app/Providers/AppServiceProvider.php` defines their IP and token-fingerprint limits. The active rate-limiter store, gateway throttles, and number of application instances are **unknown**.
- `config/queue.php:16` uses `QUEUE_CONNECTION` with a database fallback and defines a Redis queue option (`config/queue.php:67-74`). The invitation job is queued (`app/Jobs/SendApplicantInvitation.php:10-27`), but the live queue driver and worker topology were not verified.

## Cache opportunities and safety rules

| Candidate | Conditions before use | Suggested initial TTL and invalidation to evaluate |
| --- | --- | --- |
| Public **published-only** job list/details | First create a separately authorized public read path that cannot reveal drafts or private fields. Measure repeated reads and database cost. | Consider 30-120 seconds, keyed by filters/page/publication state. Invalidate or version keys on publish, edit, close, or delete. |
| Published form structure without invitation identity | Cache only a stable version's sections, fields, options, and public validation metadata; never the invitation email or token response. Confirm versions are immutable or invalidate on edits/assignment changes. | Consider 5-15 minutes keyed by version ID and any locale. Invalidate on version/field/option changes or unpublication. |
| Non-sensitive reference data | Confirm it is public and reused often. | Consider minutes with explicit invalidation when HRIS changes it. |

Do not cache raw invitation tokens, HMAC/API secrets, credentials, applicant answers, private applicant details, or one-time invitation authorization as the source of truth. Scope any authorized per-user cache by identity, tenant, permission, and environment. Do not let cached permissions outlive revocation. Avoid caching a response that contains invitation email (`app/Http/Controllers/ApplicationFormController.php:33`) in a shared key.

## Distributed reliability and rate limiting

| Store/topology | What source alone can establish | Review before multi-instance use |
| --- | --- | --- |
| Database cache fallback | Configured option, potentially shared if all instances use the same database. | Verify actual store, atomic `add` behavior, lock tables, connection health, and operational load before relying on it for nonce replay or rate limits. No database access is authorized here. |
| Local file or array cache | Configured options; not proven active. | Local state is not a shared authority across instances; a file cache on each host cannot enforce a global token or IP budget. |
| Redis | Configured option; not proven active or required. | Shared storage could support distributed rate limiting, nonce keys, locks, and queues, but adds infrastructure cost, monitoring, and failure modes. Confirm traffic and topology first. |

Rate limiting must use a store that is consistent across all instances enforcing a given budget. The actual rate-limiter store is **not verifiable from the inspected source**. Combine service identity, trusted client IP, endpoint, and one-way token fingerprint as specified in [API boundaries](API_BOUNDARIES.md); never use raw tokens as keys or logs.

## Phased Redis decision

1. **Baseline:** Measure route traffic, cache hit opportunities, database load, rate-limit and nonce requirements, instance count, and failure behavior. Confirm the current store through a separately approved operational review; this task does not access runtime configuration.
2. **Use existing Laravel cache safely:** Prefer small, non-sensitive published-data caching only after an endpoint and invalidation policy are approved. Verify consistent shared storage before enforcing distributed limits.
3. **Adopt Redis only if justified:** If multiple instances, traffic, or atomic shared-state requirements exceed the current store, propose the exact cache/rate-limit/queue role, security controls, TTLs, key namespace, capacity, monitoring, and deployment cost. Obtain separate approvals for configuration and infrastructure changes. Do not install Redis or change drivers in this documentation task.
4. **Controlled rollout and rollback:** In an authorized later task, test a shared store in an isolated environment, verify 429 behavior and replay resistance across instances, monitor latency/errors/memory/evictions, and define a rollback that preserves security invariants. Do not silently fall back to per-instance state for one-time or rate-limit decisions.

Redis remains an **optional future strategy**, not a current dependency or verified runtime service.
