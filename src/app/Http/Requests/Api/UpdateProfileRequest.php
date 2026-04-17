<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for profile updates.
 */
class UpdateProfileRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'avatar' => 'sometimes|nullable|string|max:500',
            'bio' => 'sometimes|nullable|string|max:1000',
            'city' => 'sometimes|nullable|string|max:255',
            'level' => 'sometimes|in:beginner,intermediate,pro',
            'sport_category_ids' => 'sometimes|array',
            'sport_category_ids.*' => 'integer|exists:sport_categories,id',
        ];
    }
}
