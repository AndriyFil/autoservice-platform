<?php

namespace App\Http\Requests;

use App\Enums\BookingRequestCancellationActorEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CancelBookingRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'actor' => ['required', 'string', Rule::enum(BookingRequestCancellationActorEnum::class)],
            'reason' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
