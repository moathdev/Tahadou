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
        $uuid = $this->route('uuid');

        return [
            'name'         => ['required', 'string', 'min:2', 'max:100'],
            'phone_number' => [
                'required',
                'string',
                'regex:/^[0-9\+\-\s]{7,20}$/',
                Rule::unique('participants', 'phone_number')
                    ->where(function ($query) use ($uuid) {
                        return $query->whereHas('group', function ($q) use ($uuid) {
                            $q->where('uuid', $uuid);
                        });
                    }),
            ],
            'interests'    => ['required', 'array', 'min:1', 'max:3'],
            'interests.*'  => ['string', Rule::in(array_keys(config('tahadou.interests')))],
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.unique'  => 'This phone number is already registered in this group.',
            'phone_number.regex'   => 'Please enter a valid phone number.',
            'interests.min'        => 'Please select at least 1 interest.',
            'interests.max'        => 'You can select up to 3 interests.',
        ];
    }
}
