<?php

return [
    'accepted'             => 'The :attribute field must be accepted.',
    'array'                => 'The :attribute field must be an array.',
    'between'              => [
        'array'   => 'The :attribute field must have between :min and :max items.',
        'numeric' => 'The :attribute field must be between :min and :max.',
        'string'  => 'The :attribute field must be between :min and :max characters.',
    ],
    'boolean'              => 'The :attribute field must be true or false.',
    'confirmed'            => 'The :attribute field confirmation does not match.',
    'email'                => 'The :attribute field must be a valid email address.',
    'exists'               => 'The selected :attribute is invalid.',
    'in'                   => 'The selected :attribute is invalid.',
    'integer'              => 'The :attribute field must be an integer.',
    'max'                  => [
        'array'   => 'The :attribute field must not have more than :max items.',
        'numeric' => 'The :attribute field must not be greater than :max.',
        'string'  => 'The :attribute field must not be greater than :max characters.',
    ],
    'min'                  => [
        'array'   => 'The :attribute field must have at least :min items.',
        'numeric' => 'The :attribute field must be at least :min.',
        'string'  => 'The :attribute field must be at least :min characters.',
    ],
    'numeric'              => 'The :attribute field must be a number.',
    'regex'                => 'The :attribute field format is invalid.',
    'required'             => 'The :attribute field is required.',
    'string'               => 'The :attribute field must be a string.',
    'unique'               => 'The :attribute has already been taken.',

    'custom' => [
        'name' => [
            'min'   => 'Name must be at least 3 characters.',
            'regex' => 'Name must contain letters only (no numbers or symbols).',
        ],
        'phone_number' => [
            'unique' => 'This phone number is already registered in this group.',
            'regex'  => 'Enter a valid Saudi mobile number (e.g. 0512345678 or +966512345678).',
        ],
        'interests' => [
            'min' => 'Please select at least 1 interest.',
            'max' => 'You can select up to 3 interests.',
        ],
        'admin_code' => [
            'required' => 'Admin code is required.',
        ],
    ],

    'attributes' => [
        'name'             => 'name',
        'phone_number'     => 'phone number',
        'interests'        => 'interests',
        'admin_code'       => 'admin code',
        'max_participants' => 'maximum participants',
    ],
];
