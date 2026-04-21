<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'cycle_id' => ['required', 'integer', 'exists:income_cycles,id'],
            'transaction_type' => ['required', 'in:income,expense'],
            'category_id' => ['nullable', 'required_if:transaction_type,expense', 'integer', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'timestamp' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
