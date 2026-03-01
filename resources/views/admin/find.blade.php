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
        <div class="space-y-5" id="find-form">

            <div>
                <label for="group_uuid" class="block text-sm font-medium text-gray-600 mb-1">
                    {{ __('app.admin_find_uuid_label') }}
                </label>
                <input
                    type="text"
                    id="group_uuid"
                    name="group_uuid"
                    placeholder="{{ __('app.admin_find_uuid_placeholder') }}"
                    autofocus
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-violet-400 outline-none transition text-sm font-mono tracking-wider text-center"
                    dir="ltr"
                />
                <p id="uuid-error" class="text-red-500 text-xs mt-1 hidden">{{ __('app.admin_find_uuid_required') }}</p>
            </div>

            <button
                type="button"
                onclick="goToAdmin()"
                class="w-full py-3 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold transition text-sm"
            >
                {{ __('app.admin_find_btn') }}
            </button>
        </div>
    </div>

    <p class="text-center text-xs text-gray-400 mt-6">
        {{ __('app.admin_find_hint') }}
    </p>
</div>
@endsection

@push('scripts')
<script>
    // Allow Enter key to submit
    document.getElementById('group_uuid').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') goToAdmin();
    });

    function goToAdmin() {
        const uuid = document.getElementById('group_uuid').value.trim();
        const err  = document.getElementById('uuid-error');

        if (!uuid) {
            err.classList.remove('hidden');
            return;
        }

        err.classList.add('hidden');

        // Redirect to the admin login page for this group UUID
        window.location.href = '/admin/' + encodeURIComponent(uuid) + '/login';
    }
</script>
@endpush
