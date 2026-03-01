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
        $uuid    = $this->route('uuid');
        $group   = \App\Models\Group::where('uuid', $uuid)->first();
        $groupId = $group?->id;

        return [
            // At least 3 characters, letters/spaces/Arabic only (no single-char tricks)
            'name'         => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s\-\.]+$/u'],
            'phone_number' => [
                'required',
                'string',
                // Saudi mobile: 05XXXXXXXX or +9665XXXXXXXX or 9665XXXXXXXX
                'regex:/^(\+?966|0)5[0-9]{8}$/',
                Rule::unique('participants', 'phone_number')
                    ->where('group_id', $groupId),
            ],
            'interests'    => ['required', 'array', 'min:1', 'max:3'],
            'interests.*'  => ['string', Rule::in(config('tahadou.interests'))],
        ];
    }

    public function messages(): array
    {
        return [
            'name.min'             => __('validation.custom.name.min'),
            'name.regex'           => __('validation.custom.name.regex'),
            'phone_number.unique'  => __('validation.custom.phone_number.unique'),
            'phone_number.regex'   => __('validation.custom.phone_number.regex'),
            'interests.min'        => __('validation.custom.interests.min'),
            'interests.max'        => __('validation.custom.interests.max'),
        ];
    }
}
