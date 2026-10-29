# Change Approval

Before future work on a protected JAO path, present a concrete proposal. Approval is scoped to the exact files, commands, target environment, and operations listed. An approved audit, plan, or general request to fix security issues does not authorize database access, secret access, migration work, or unrelated code changes.

## Proposal checklist

1. **Files affected:** exact existing and new paths; identify protected paths.
2. **Purpose:** the user-visible defect or security invariant and the smallest proposed change.
3. **Security impact:** authentication, authorization, sensitive data, abuse, and failure modes.
4. **Database impact:** whether any database connection, read, write, test database, or data backfill is required; name the target environment.
5. **Migration impact:** whether a migration file or migration command is required; provide a separate approval request for each.
6. **Testing approach:** exact tests or static checks, their database behavior, and any prohibited commands.
7. **Rollback procedure:** how to reverse code and data changes safely without losing existing records.

Request explicit approval only after the proposal is reviewable. Record which operations were approved; do not infer approval for adjacent files or commands. If the task changes scope, ask again before crossing the new boundary. A new user instruction may grant scope only when it explicitly names the protected operation and does not conflict with a higher-priority safety prohibition.

For the initial security audit, approval covered documentation files listed in `PROTECTED_FILES.md` only. The later Laravel Specialist setup separately authorizes its standalone agent TOML and minimal guardrail documentation updates. No database, migration, application-code, test, dependency, deployment, or secret operation was approved by either task.
