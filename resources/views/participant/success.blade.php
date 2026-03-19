@extends('layouts.app')

@section('title', __('app.success_title') . ' — ' . __('app.app_name'))

@section('content')
<div class="max-w-md mx-auto text-center">
    <div class="text-7xl mb-6">🎊</div>
    <h1 class="text-3xl font-bold text-violet-800 mb-3">{{ __('app.success_title') }}</h1>
    <p class="text-gray-600 mb-2">
        {{ __('app.success_subtitle', ['group' => $group->name]) }}
    </p>
    <p class="text-gray-500 text-sm mb-8">
        {{ __('app.success_body') }}
    </p>

    <div class="bg-violet-50 rounded-2xl border border-violet-100 p-6 text-start space-y-3">
        <h2 class="font-semibold text-violet-700 text-sm">{{ __('app.success_next') }}</h2>
        <ul class="text-sm text-gray-600 space-y-2">
            <li>{{ __('app.success_step1') }}</li>
            <li>{{ __('app.success_step2') }}</li>
            <li>{{ __('app.success_step3') }}</li>
            <li>{{ __('app.success_step4') }}</li>
        </ul>
    </div>

    <p class="text-xs text-gray-400 mt-8">{{ __('app.success_greeting') }}</p>
</div>
@endsection
