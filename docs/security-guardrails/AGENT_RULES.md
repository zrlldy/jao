# Rules for All JAO Agents

These rules bind Codex, the project `laravel_specialist`, Superpowers, Ponytail, plugins, automation, and every subagent in every execution mode. Pass this file and the current task restrictions to any delegated worker before it acts.

1. Read root `AGENTS.md`, `AI_MASTER_GUIDELINES.md`, `SECURITY_POLICY.md`, `PROTECTED_FILES.md`, and this file before JAO work; read `CHANGE_APPROVAL.md` before proposing a protected change.
2. Respect protected paths and never inspect environment secrets, private keys, credentials, or secret token values.
3. Change only files and perform only operations explicitly approved for the current task. An audit or plan is not permission to implement.
4. Never bypass, weaken, or remove authentication, authorization, validation, rate controls, or data-protection checks to simplify code or testing.
5. Never connect to a database, run database-dependent tests, or execute or edit migrations without separate, explicit permission for the operation and target environment.
6. Avoid destructive commands, resets, discards, commits, pushes, deployments, installers, generators, and automatic fixes unless each is within approved scope.
7. Preserve existing application behavior and user changes; investigate concurrent or unexpected worktree changes before proceeding.
8. Favor a secure, minimal Laravel-native solution when implementation is approved. Ponytail never overrides a security boundary.
9. Support every audit finding with source path, exact line, concrete scenario, and confirmation status. Mark deployment-dependent claims as potential or needing verification.
10. Stop a prohibited action before execution and explain the restriction and the separate approval required. Never use another tool, script, hook, or subagent to get around it.
11. Propagate all restrictions and the exact approved scope to subagents; review their proposed tool use and output before accepting it.
12. Verify documentation against the source and the scoped Git diff. Do not claim runtime, database, or migration behavior was tested when it was not.
13. Follow the Task History Protocol in `AI_MASTER_GUIDELINES.md`: all agents, including the Laravel Specialist, Superpowers, Ponytail, and subagents, read the three shared `docs/task-history/` files before development and update them only with evidence-backed progress. Preserve prior entries; task history never grants approval for protected operations.

Conflict order: explicit user safety prohibitions, these JAO guardrails, task-specific approved scope, Superpowers workflows, then Ponytail recommendations. Higher-priority restrictions win.
