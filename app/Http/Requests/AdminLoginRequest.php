<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin_code' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'admin_code.required' => 'Admin code is required.',
        ];
    }
}
