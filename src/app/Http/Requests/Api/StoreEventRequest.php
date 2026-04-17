<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for creating an event.
 */
class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'spot_id' => 'nullable|integer|exists:spots,id',
            'date' => 'required|date|after:now',
            'max_participants' => 'sometimes|integer|min:2|max:100',
        ];
    }
}