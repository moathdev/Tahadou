@extends('layouts.app')

@section('title', __('app.home_title'))

@section('content')
<div class="max-w-lg mx-auto">
    <!-- Hero -->
    <div class="text-center mb-10">
        <div class="text-6xl mb-4">🎁</div>
        <h1 class="text-4xl font-bold text-violet-800 mb-2">{{ __('app.home_hero_title') }}</h1>
        <p class="text-gray-500 text-lg">{{ __('app.home_hero_subtitle') }}</p>
    </div>

    <!-- Create Group Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-violet-100 p-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-6">{{ __('app.create_group_heading') }}</h2>

        <form action="{{ route('group.create') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-600 mb-1">
                    {{ __('app.group_name_label') }}
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="{{ __('app.group_name_placeholder') }}"
                    required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-violet-400 focus:border-violet-400 outline-none transition text-sm"
                />
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="max_participants" class="block text-sm font-medium text-gray-600 mb-1">
                    {{ __('app.max_participants_label') }}
                </label>
                <input
                    type="number"
                    id="max_participants"
                    name="max_participants"
                    value="{{ old('max_participants', 30) }}"
                    min="3"
                    max="200"
                    required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-violet-400 focus:border-violet-400 outline-none transition text-sm"
                />
                <p class="text-xs text-gray-400 mt-1">{{ __('app.max_participants_hint') }}</p>
                @error('max_participants')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="max_gift_price" class="block text-sm font-medium text-gray-600 mb-1">
                    {{ __('app.max_gift_price_label') }}
                </label>
                <input
                    type="number"
                    id="max_gift_price"
                    name="max_gift_price"
                    value="{{ old('max_gift_price') }}"
                    min="1"
                    placeholder="{{ __('app.max_gift_price_placeholder') }}"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-violet-400 focus:border-violet-400 outline-none transition text-sm"
                />
                <p class="text-xs text-gray-400 mt-1">{{ __('app.max_gift_price_hint') }}</p>
                @error('max_gift_price')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full py-3 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold transition text-sm tracking-wide shadow"
            >
                {{ __('app.create_group_btn') }}
            </button>
        </form>
    </div>

    <!-- How it works -->
    <div class="mt-10 grid grid-cols-3 gap-4 text-center">
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <div class="text-2xl mb-2">🔗</div>
            <p class="text-xs text-gray-500">{{ __('app.how_share') }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <div class="text-2xl mb-2">📝</div>
            <p class="text-xs text-gray-500">{{ __('app.how_register') }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <div class="text-2xl mb-2">🎯</div>
            <p class="text-xs text-gray-500">{{ __('app.how_draw') }}</p>
        </div>
    </div>
</div>
@endsection
