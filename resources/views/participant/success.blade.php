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

    @if(session('updated'))
    <div class="mb-6 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-medium">
        ✅ {{ __('app.edit_saved') }}
    </div>
    @endif

    <div class="bg-violet-50 rounded-2xl border border-violet-100 p-6 text-start space-y-3">
        <h2 class="font-semibold text-violet-700 text-sm">{{ __('app.success_next') }}</h2>
        <ul class="text-sm text-gray-600 space-y-2">
            <li>{{ __('app.success_step1') }}</li>
            <li>{{ __('app.success_step2') }}</li>
            <li>{{ __('app.success_step3') }}</li>
            <li>{{ __('app.success_step4') }}</li>
        </ul>
    </div>

    {{-- Edit link — only show if draw hasn't been done and token is available --}}
    @if($editToken && $participant && ! $group->is_drawn)
    <div class="mt-6 bg-amber-50 rounded-xl border border-amber-200 p-4 text-sm text-amber-800">
        <p class="font-medium mb-2">{{ __('app.edit_hint') }}</p>
        <a
            href="{{ route('participant.edit', ['uuid' => $group->uuid, 'editToken' => $editToken]) }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold transition"
        >
            ✏️ {{ __('app.edit_btn') }}
        </a>
    </div>
    @elseif($group->is_drawn)
    <div class="mt-6 text-xs text-gray-400">
        {{ __('app.edit_draw_done') }}
    </div>
    @endif

    <p class="text-xs text-gray-400 mt-8">{{ __('app.success_greeting') }}</p>
</div>
@endsection
