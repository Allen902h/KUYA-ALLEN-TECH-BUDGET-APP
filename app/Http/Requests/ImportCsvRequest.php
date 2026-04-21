<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportCsvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'cycle_id' => ['required', 'integer', 'exists:income_cycles,id'],
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ];
    }
}