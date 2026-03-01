<?php

return [
    'accepted'             => 'يجب قبول حقل :attribute.',
    'array'                => 'يجب أن يكون حقل :attribute مصفوفة.',
    'between'              => [
        'array'   => 'يجب أن يحتوي حقل :attribute على ما بين :min و :max عناصر.',
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'string'  => 'يجب أن يكون عدد محارف :attribute بين :min و :max.',
    ],
    'boolean'              => 'يجب أن يكون حقل :attribute صحيحاً أو خاطئاً.',
    'confirmed'            => 'تأكيد :attribute غير مطابق.',
    'date'                 => ':attribute ليس تاريخاً صحيحاً.',
    'distinct'             => 'حقل :attribute يحتوي على قيمة مكررة.',
    'email'                => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيح.',
    'exists'               => 'القيمة المحددة في :attribute غير صحيحة.',
    'in'                   => 'القيمة المحددة في :attribute غير صحيحة.',
    'integer'              => 'يجب أن يكون :attribute عدداً صحيحاً.',
    'json'                 => 'يجب أن يكون :attribute نص JSON صحيح.',
    'max'                  => [
        'array'   => 'يجب أن لا يحتوي :attribute على أكثر من :max عناصر.',
        'numeric' => 'يجب أن تكون قيمة :attribute أصغر من أو تساوي :max.',
        'string'  => 'يجب أن لا يتجاوز :attribute :max محارف.',
    ],
    'min'                  => [
        'array'   => 'يجب أن يحتوي :attribute على الأقل على :min عناصر.',
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من أو تساوي :min.',
        'string'  => 'يجب أن يحتوي :attribute على الأقل على :min محارف.',
    ],
    'not_in'               => 'القيمة المحددة في :attribute غير صحيحة.',
    'numeric'              => 'يجب أن يكون :attribute رقماً.',
    'present'              => 'يجب تقديم حقل :attribute.',
    'regex'                => 'صيغة :attribute غير صحيحة.',
    'required'             => 'حقل :attribute مطلوب.',
    'required_if'          => 'حقل :attribute مطلوب عندما يكون :other يساوي :value.',
    'required_unless'      => 'حقل :attribute مطلوب ما لم يكن :other ضمن :values.',
    'required_with'        => 'حقل :attribute مطلوب عندما يكون :values موجوداً.',
    'required_with_all'    => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_without'     => 'حقل :attribute مطلوب عندما لا يكون :values موجوداً.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا يكون أي من :values موجوداً.',
    'same'                 => 'يجب أن يتطابق :attribute مع :other.',
    'size'                 => [
        'array'   => 'يجب أن يحتوي :attribute على :size عناصر.',
        'numeric' => 'يجب أن تكون قيمة :attribute :size.',
        'string'  => 'يجب أن يحتوي :attribute على :size محارف.',
    ],
    'string'               => 'يجب أن يكون :attribute نصاً.',
    'timezone'             => 'يجب أن يكون :attribute منطقة زمنية صحيحة.',
    'unique'               => 'قيمة :attribute مستخدمة من قبل.',
    'url'                  => 'صيغة :attribute غير صحيحة.',

    'custom' => [
        'name' => [
            'min'   => 'الاسم يجب أن يكون 3 أحرف على الأقل.',
            'regex' => 'الاسم يجب أن يحتوي على أحرف فقط (لا أرقام أو رموز).',
        ],
        'phone_number' => [
            'unique' => 'رقم الهاتف هذا مسجّل بالفعل في هذه المجموعة.',
            'regex'  => 'أدخل رقم جوال سعودي صحيح (مثال: 0512345678 أو +966512345678).',
        ],
        'interests' => [
            'min' => 'الرجاء اختيار اهتمام واحد على الأقل.',
            'max' => 'يمكنك اختيار 3 اهتمامات كحد أقصى.',
        ],
        'admin_code' => [
            'required' => 'كود المشرف مطلوب.',
        ],
    ],

    'attributes' => [
        'name'             => 'الاسم',
        'phone_number'     => 'رقم الهاتف',
        'interests'        => 'الاهتمامات',
        'admin_code'       => 'كود المشرف',
        'max_participants' => 'الحد الأقصى للمشتركين',
    ],
];
