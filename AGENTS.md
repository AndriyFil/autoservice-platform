# AGENTS.md

## Project context

This project is a Laravel learning project for AutoService / booking platform.
Use Context7 to check the current Laravel documentation before suggesting the solution.

The main goal is to learn Laravel correctly, not just generate code quickly.
Prefer simple, readable Laravel conventions unless the task clearly needs more architecture.

## How to work

- Before coding, briefly explain the intended change.
- Do not over-engineer.
- Do not introduce DDD, CQRS, Event Sourcing, repositories, services, actions, or DTOs unless they solve a real problem in this task.
- Prefer standard Laravel features: Eloquent models, Form Requests, Policies, Services only when needed.
- Keep changes small and reviewable.
- If the task is ambiguous, inspect the code first and make the safest assumption.

## Code style

- Follow PSR-12.
- Use strict typing where practical.
- Avoid duplicated logic.
- Avoid large controllers.
- Keep business rules close to the model/domain behavior when it is simple.
- Use Form Requests for HTTP validation.
- Use enums for fixed statuses/types.
- Do not use magic strings for statuses.
- Do not add unused abstractions.

## Testing

When changing business behavior:

- Add or update Feature tests for HTTP/API behavior.
- Add Unit tests only for isolated business logic.
- Run relevant tests before finishing.
- If tests cannot be run, explain why.

## Done when

A task is done only when:

- The requested behavior is implemented.
- Existing behavior is not broken.
- Relevant tests pass or the reason they were not run is stated.
- The final answer includes changed files and verification steps.

## Review guidelines

When reviewing code, focus on:

- broken business rules
- invalid status transitions
- missing validation
- security issues
- N+1 queries
- wrong relationships
- over-engineering
- missing tests
