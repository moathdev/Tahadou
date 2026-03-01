@extends('layouts.app')

@section('title', __('app.app_name'))

@section('content')
<div class="max-w-md mx-auto text-center">
    @if($reason === 'draw_executed')
        <div class="text-6xl mb-4">✅</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ __('app.closed_draw_title') }}</h1>
        <p class="text-gray-500">{{ __('app.closed_draw_body', ['group' => $group->name]) }}</p>

    @elseif($reason === 'locked')
        <div class="text-6xl mb-4">🔒</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ __('app.closed_lock_title') }}</h1>
        <p class="text-gray-500">{{ __('app.closed_lock_body', ['group' => $group->name]) }}</p>

    @elseif($reason === 'full')
        <div class="text-6xl mb-4">🈵</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ __('app.closed_full_title') }}</h1>
        <p class="text-gray-500">{{ __('app.closed_full_body', ['group' => $group->name, 'max' => $group->max_participants]) }}</p>
    @endif

    <a href="{{ route('home') }}" class="mt-8 inline-block px-6 py-3 rounded-xl bg-violet-600 text-white text-sm font-medium hover:bg-violet-700 transition">
        {{ __('app.create_your_own') }}
    </a>
</div>
@endsection
