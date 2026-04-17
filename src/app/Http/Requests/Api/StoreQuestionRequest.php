<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for creating a question.
 */
class StoreQuestionRequest extends FormRequest
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
            'content' => 'nullable|string',
            'spot_id' => 'nullable|integer|exists:spots,id',
            'sport_category_id' => 'nullable|integer|exists:sport_categories,id',
        ];
    }
}
