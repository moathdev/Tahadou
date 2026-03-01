@extends('layouts.app')

@section('title', __('app.admin_find_title') . ' — ' . __('app.app_name'))

@section('content')
<div class="max-w-sm mx-auto">
    <div class="text-center mb-8">
        <div class="text-4xl mb-3">🔐</div>
        <h1 class="text-2xl font-bold text-gray-800">{{ __('app.admin_find_title') }}</h1>
        <p class="text-gray-500 text-sm mt-1">{{ __('app.admin_find_subtitle') }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <form action="{{ route('admin.find.submit') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="admin_code" class="block text-sm font-medium text-gray-600 mb-1">
                    {{ __('app.admin_code_label') }}
                </label>
                <input
                    type="text"
                    id="admin_code"
                    name="admin_code"
                    value="{{ old('admin_code') }}"
                    placeholder="{{ __('app.admin_code_placeholder') }}"
                    required
                    autofocus
                    autocomplete="off"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-violet-400 outline-none transition text-sm font-mono uppercase tracking-widest text-center"
                    dir="ltr"
                />
                @error('admin_code')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full py-3 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold transition text-sm"
            >
                {{ __('app.admin_find_btn') }}
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-gray-400 mt-6">
        {{ __('app.admin_find_hint') }}
    </p>
</div>
@endsection

@push('scripts')
<script>
    // Auto-uppercase as user types
    document.getElementById('admin_code').addEventListener('input', function () {
        const pos = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(pos, pos);
    });
</script>
@endpush
