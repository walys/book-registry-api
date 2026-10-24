<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'regex:/^[0-9]+$/'],
            'value' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
