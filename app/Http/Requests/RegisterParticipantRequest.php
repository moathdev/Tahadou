<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // At least 3 characters, letters/spaces/Arabic only (no single-char tricks)
            'name'         => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s\-\.]+$/u'],
            'phone_number' => [
                'required',
                'string',
                // Saudi mobile: 05XXXXXXXX or +9665XXXXXXXX or 9665XXXXXXXX
                // Duplicates are allowed — a parent may register multiple children under their number
                'regex:/^(\+?966|0)5[0-9]{8}$/',
            ],
            'gender'       => ['required', Rule::in(['male', 'female', 'child'])],
            'interests'    => ['required', 'array', 'min:1', 'max:3'],
            'interests.*'  => ['string', Rule::in(config('tahadou.interests'))],
        ];
    }

    public function messages(): array
    {
        return [
            'name.min'             => __('validation.custom.name.min'),
            'name.regex'           => __('validation.custom.name.regex'),
            'phone_number.regex'   => __('validation.custom.phone_number.regex'),
            'interests.min'        => __('validation.custom.interests.min'),
            'interests.max'        => __('validation.custom.interests.max'),
        ];
    }
}
