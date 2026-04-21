<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'is_fixed' => ['nullable', 'boolean'],
            'budget_limit' => ['nullable', 'numeric', 'min:0'],
            'due_day' => ['nullable', 'integer', 'min:1', 'max:31'],
        ];
    }
}
