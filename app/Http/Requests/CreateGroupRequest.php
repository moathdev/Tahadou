<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'min:2', 'max:100'],
            'max_participants' => ['required', 'integer', 'min:3', 'max:200'],
            'max_gift_price'   => ['nullable', 'integer', 'min:1', 'max:100000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                  => 'Group name is required.',
            'max_participants.required'      => 'Maximum participants is required.',
            'max_participants.min'           => 'A group needs at least 3 participants.',
        ];
    }
}
