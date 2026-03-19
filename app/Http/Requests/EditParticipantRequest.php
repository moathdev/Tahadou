<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s\-\.]+$/u'],
            'gender'    => ['required', Rule::in(['male', 'female', 'child'])],
            'interests' => ['required', 'array', 'min:1', 'max:3'],
            'interests.*' => ['string', Rule::in(config('tahadou.interests'))],
        ];
    }

    public function messages(): array
    {
        return [
            'name.min'       => __('validation.custom.name.min'),
            'name.regex'     => __('validation.custom.name.regex'),
            'interests.min'  => __('validation.custom.interests.min'),
            'interests.max'  => __('validation.custom.interests.max'),
        ];
    }
}
