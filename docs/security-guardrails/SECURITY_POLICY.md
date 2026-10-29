# JAO Security Policy

This policy applies to JAO development, reviews, automation, and AI agents. Read `PROTECTED_FILES.md`, `AGENT_RULES.md`, and `CHANGE_APPROVAL.md` before proposing or making sensitive changes. A written guardrail does not replace runtime access control or human review.

## Core standards

- Grant each human and service only the operations and records it needs. A shared API key may authenticate a service, but must not be treated as user identity or record-level authorization.
- Authenticate every protected request and authorize each read, write, relationship change, and deletion against the acting principal. Keep applicant invitation access scoped to its job and form version.
- Treat invitation links as bearer credentials. Generate unpredictable tokens, store only a hash, enforce expiration and one-time use, and avoid putting raw tokens in logs, responses, or durable queue payloads. Confirm replay protection at the database boundary when concurrent requests matter.
- Validate all external input at the boundary: UUID shape, ownership, allowed states, field type and value, payload size, and uploads. Pass only validated fields to writes. Do not use client-supplied IDs without checking their relationship to the authorized parent.
- Preserve submitted applications and answers. Review every cascade and deletion path before changing forms, jobs, invitations, or applicant data. Use transactions and database constraints where application checks alone cannot enforce an invariant.
- Return only fields needed by the caller. Exclude token hashes, credentials, private applicant data, and internal metadata from API resources unless the endpoint explicitly requires them and authorizes access.
- Return clear 4xx responses for invalid or unauthorized requests; do not expose stack traces, SQL, tokens, or secret values. Log only the minimum diagnostic context and redact sensitive values.
- Review CSRF, CORS, rate limiting, cache sharing, and cookie settings for the actual client and deployment model. Do not assume a test-only middleware protects production routes.
- Prefer existing Laravel controls and the smallest secure fix. Never remove authentication, authorization, validation, auditability, or data protection to shorten code.

## Protected resources and approval

- Never access environment files, private keys, credentials, access tokens, or secret values. Do not print them through commands, logs, tests, or tools.
- Treat `database/migrations/**` as read-only by default. Never run a migration or any database operation without separate, explicit approval for the exact operation and target environment. This audit authorized no database access and no migration changes.
- Controllers, models, routes, middleware, policies, form requests, services, API authentication, and submission logic require a scoped change proposal and explicit approval before future edits. Follow `CHANGE_APPROVAL.md`.
- The initial security audit authorized only `docs/security-guardrails/**` and the security addition to root `AGENTS.md`. The subsequent Laravel Specialist setup additionally authorizes `.codex/agents/laravel-specialist.toml` and the minimum guardrail documentation updates. Neither task authorizes application fixes, tests, formatters, dependency changes, commits, or deployment.

If an instruction conflicts with these restrictions, stop that action and explain the conflict and required approval. Do not substitute an indirect script, generated file, alternate tool, or subagent.
