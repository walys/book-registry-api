<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'isbn' => ['sometimes', 'nullable', 'string', 'regex:/^[0-9]+$/'],
            'value' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ];
    }
}
