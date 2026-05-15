# Laravel AutoService Platform MVP Summary

This is a learning Laravel project, but the target direction is production-oriented and potentially sellable.

## Product Context

The app is an AutoService Platform MVP where customers can create booking requests for workshops.

Current implemented domain area:

- Public workshop booking request creation flow
- Booking request statuses
- Confirm/reject/cancel booking request transitions
- Metadata for confirm/reject/cancel operations
- API resources for returning booking request data
- Validation via Laravel FormRequests
- Business rules enforced on the `BookingRequest` model
- Invalid status transitions return API `409 Conflict`

## Main Domain Entity

The central model is:

- `App\Models\BookingRequest`

Important related models:

- `Workshop`
- `Customer`
- `Vehicle`
- `WorkshopService`
- `BookingRequestStatus`
- `BookingRequestCancellationActor`

`BookingRequest` uses UUIDs.

## Booking Request Statuses

Statuses are stored in the `booking_request_statuses` reference table and represented by:

- `App\Enums\BookingRequestStatusEnum`

Current enum values:

- `New = 1`
- `Confirmed = 2`
- `RescheduleRequested = 3`
- `Rejected = 4`
- `Cancelled = 5`
- `ConvertedToRepairOrder = 6`

`ConvertedToRepairOrder` exists as a future status, but repair order conversion is not implemented yet because there is no `RepairOrder` entity.

## Cancellation Actors

Cancellation actors are normalized through a reference table:

- `booking_request_cancellation_actors`

Seeded static records:

- `1 | customer | Customer`
- `2 | workshop | Workshop`
- `3 | system | System`

They are represented by:

- `App\Enums\BookingRequestCancellationActorEnum`
- `App\Models\BookingRequestCancellationActor`

The frontend sends cancellation actor codes backed by `BookingRequestCancellationActorEnum`.

Example cancel payload:

```json
{
  "actor": "customer",
  "reason": "Customer no longer needs service."
}
```

The API response exposes the cancellation actor code from the enum:

```json
{
  "cancellation_actor": {
    "code": "customer",
    "name": "Customer"
  }
}
```

## Booking Request Metadata

`booking_requests` has transition metadata fields:

- `confirmed_at`
- `rejected_at`
- `cancelled_at`
- `rejected_reason`
- `cancelled_reason`
- `cancellation_actor_id`

Rejected and cancelled reasons are free text for now. There are no reason dictionary tables yet.

## Business Rules

Transition rules live in `App\Models\BookingRequest`.

### Confirm

Method:

```php
$bookingRequest->confirm();
```

Rules:

- Allowed only from `New`
- Sets status to `Confirmed`
- Sets `confirmed_at`

### Reject

Method:

```php
$bookingRequest->reject($reason);
```

Rules:

- Allowed only from `New`
- Sets status to `Rejected`
- Sets `rejected_reason`
- Sets `rejected_at`

### Cancel

Method:

```php
$bookingRequest->cancel($actor, $reason);
```

Rules:

- Allowed from `New` or `Confirmed`
- Sets status to `Cancelled`
- Sets `cancellation_actor_id`
- Sets `cancelled_reason`
- Sets `cancelled_at`

Invalid transitions throw:

- `App\Exceptions\BookingRequest\InvalidBookingRequestStatusTransitionException`

For API requests, this exception is rendered as:

- HTTP `409 Conflict`

## Actions

Actions are simple application operation classes. They do not duplicate transition rules.

Current actions:

- `CreateBookingRequestAction`
- `ConfirmBookingRequestAction`
- `RejectBookingRequestAction`
- `CancelBookingRequestAction`

Pattern:

1. Call the model business method.
2. Save the model.
3. Return the model.

## Data Objects

Data objects are used where they reduce controller noise.

Current data objects:

- `CreateBookingRequestData`
- `CancelBookingRequestData`

There is no `RejectBookingRequestData` because reject currently has only one parameter, `reason`, and a data class for that was considered over-engineering.

`CancelBookingRequestData` is useful because it converts the validated actor code into `BookingRequestCancellationActorEnum`.

## API Endpoints

Current booking request endpoints:

```http
POST /api/workshops/{workshop}/booking-requests
POST /api/booking-requests/{bookingRequest}/confirm
POST /api/booking-requests/{bookingRequest}/reject
POST /api/booking-requests/{bookingRequest}/cancel
```

### Reject Payload

```json
{
  "reason": "No available slot."
}
```

Validation:

- `reason` required
- string
- max 5000

### Cancel Payload

```json
{
  "actor": "customer",
  "reason": "Customer changed plans."
}
```

Validation:

- `actor` required
- string
- must be one of enum values: `customer`, `workshop`, `system`
- `reason` nullable
- string
- max 5000

## API Resource

`BookingRequestResource` returns public booking request data including:

- `id`
- `status`
- `confirmed_at`
- `rejected_at`
- `cancelled_at`
- `rejected_reason`
- `cancelled_reason`
- `cancellation_actor.code`
- `cancellation_actor.name`
- contact snapshot
- vehicle snapshot
- request details
- `created_at`

It uses enum-backed actor codes in the public response and includes actor `name` as a readable reference-table label.

## Tests

Current tests cover:

### Unit

`tests/Unit/BookingRequestStatusTest.php`

Covered behavior:

- New booking request can be confirmed
- New booking request can be rejected with reason
- New booking request can be cancelled by customer
- Confirmed booking request can be cancelled by workshop
- Rejected booking request cannot be confirmed
- Cancelled booking request cannot be confirmed
- Rejected booking request cannot be cancelled
- Cancelled booking request cannot be cancelled again

### Feature

`tests/Feature/BookingRequestTransitionTest.php`

Covered behavior:

- `POST /confirm` changes `New` to `Confirmed`
- `POST /reject` changes `New` to `Rejected` and saves reason
- `POST /cancel` changes `New` to `Cancelled` and saves actor plus reason
- Invalid transition returns `409 Conflict`
- Reject without reason returns `422`
- Cancel without actor returns `422`
- Cancel with invalid actor returns `422`

## Design Decisions

- Keep implementation Laravel-native and simple.
- Do not add repository classes.
- Keep business transition rules in the domain model, not in controllers or actions.
- Use actions as thin command-like application operations.
- Use FormRequests for validation.
- Use data classes only when they reduce complexity.
- Use enum-backed actor codes for cancellation actor input and output.
- Expose cancellation actor code from enum in API responses.
- Keep cancellation actor `name` as a readable reference-table label.
- Do not add reason tables yet.
- Do not implement repair order conversion yet.

## Patterns Used

The project mostly follows Laravel conventions plus light domain modeling.

Patterns/concepts present:

- Domain Model: `BookingRequest` owns transition behavior and rules.
- Command-like Actions: action classes represent application operations.
- FormRequest validation boundary.
- Resource transformation for API output.

Avoided intentionally:

- Repositories
- Reason dictionary tables
- Premature RepairOrder implementation
- Over-engineered DTOs for single scalar parameters

## Current Verification

Latest test run:

```bash
composer test
```

Result:

```text
24 passed, 79 assertions
```
