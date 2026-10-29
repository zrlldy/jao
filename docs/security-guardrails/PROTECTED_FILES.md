# Protected Files and Access Levels

Apply these rules to direct and indirect access, including shell commands, search tools, browser tools, scripts, generators, formatters, test runners, plugins, and subagents. A filename being in a repository does not make its contents safe to inspect.

| Access level | Paths or resources | Rule |
| --- | --- | --- |
| **Never access** | `.env`, `.env.*`, environment-secret files anywhere, private keys, service accounts, credentials, API keys, access tokens, production and staging secrets | Do not read, print, copy, edit, delete, search within, or expose values. Do not run commands that reveal environment values. |
| **Read-only during this audit** | `database/migrations/**`, existing `app/**`, `routes/**`, `tests/**`, `config/**`, `bootstrap/**`, dependency manifests and safe project configuration | Inspect source only. No code, configuration, migration, test, dependency, or generated-file edits. No database access or application execution. |
| **Explicit approval required for future changes** | Controllers, models, routes, middleware, policies, form requests, services, API authentication components, application invitation and submission logic, and related tests or configuration | First submit the exact files, purpose, security and data impact, test approach, and rollback. Approval covers only the named operation. Migration edits and database commands each need separate approval. |
| **Writable for the initial security audit only** | `docs/security-guardrails/**` and the security instructions appended to root `AGENTS.md` | The initial audit authorized only these documentation paths. Preserve all existing `AGENTS.md` text. |

The subsequent Laravel Specialist setup explicitly authorizes `.codex/agents/laravel-specialist.toml` and the minimum needed updates to root `AGENTS.md` and `docs/security-guardrails/**`. This is a task-specific exception for agent configuration and documentation, not permission to change application code, migrations, databases, or secrets.

The master-guidelines setup separately authorizes `AI_MASTER_GUIDELINES.md` and scoped updates to `AGENTS.md`, `.codex/agents/laravel-specialist.toml`, and `docs/security-guardrails/**`. It does not authorize application code, migration, database, secret, dependency, or deployment operations.

Never modify protected files through a helper, code generation, auto-fixer, dependency install, hook, automation, or delegated agent. Do not run destructive Git commands, discard work, commit, push, or deploy as part of this audit. Stop if a tool would cross an access boundary.
