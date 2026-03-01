@extends('layouts.app')

@section('title', __('app.group_created_title') . ' — ' . __('app.app_name'))

@section('content')
<div class="max-w-lg mx-auto">
    <div class="text-center mb-8">
        <div class="text-5xl mb-3">🎊</div>
        <h1 class="text-3xl font-bold text-violet-800">{{ __('app.group_created_title') }}</h1>
        <p class="text-gray-500 mt-1">{{ __('app.group_created_subtitle') }}</p>
    </div>

    <!-- Shareable Link -->
    <div class="bg-white rounded-2xl shadow-lg border border-violet-100 p-6 mb-5">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ __('app.reg_link_heading') }}</h2>
        <p class="text-xs text-gray-400 mb-2">{{ __('app.reg_link_hint') }}</p>
        <div class="flex items-center gap-2">
            <input
                type="text"
                id="shareLink"
                value="{{ $shareableLink }}"
                readonly
                class="flex-1 text-sm px-3 py-2 bg-violet-50 border border-violet-200 rounded-lg text-violet-700 font-mono"
            />
            <button
                onclick="copyLink()"
                class="px-3 py-2 bg-violet-600 text-white rounded-lg text-sm hover:bg-violet-700 transition"
            >
                {{ __('app.copy_btn') }}
            </button>
        </div>
        <p id="copyMsg" class="text-green-600 text-xs mt-2 hidden">{{ __('app.copied') }}</p>
    </div>

    <!-- Admin Code -->
    <div class="bg-amber-50 rounded-2xl border border-amber-200 p-6 mb-5">
        <h2 class="text-sm font-semibold text-amber-700 uppercase tracking-wide mb-1">{{ __('app.admin_code_heading') }}</h2>
        <p class="text-xs text-amber-600 mb-3">{{ __('app.admin_code_warning') }}</p>
        <div class="text-3xl font-mono font-bold text-amber-800 tracking-widest text-center py-3 bg-white rounded-xl border border-amber-200">
            {{ $adminCode }}
        </div>
    </div>

    <!-- Admin Dashboard Link -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ __('app.admin_dashboard_heading') }}</h2>
        <a
            href="{{ route('admin.login', $group->uuid) }}"
            class="block w-full text-center py-3 rounded-xl bg-gray-800 text-white text-sm font-medium hover:bg-gray-900 transition"
        >
            {{ __('app.go_to_dashboard') }}
        </a>
    </div>

    <p class="text-center text-xs text-gray-400">
        {{ __('app.group_info', ['name' => $group->name, 'max' => $group->max_participants]) }}
        @if($group->max_gift_price)
            · {{ __('app.max_gift_price_badge', ['price' => number_format($group->max_gift_price)]) }}
        @endif
    </p>
</div>
@endsection

@push('scripts')
<script>
function copyLink() {
    const input = document.getElementById('shareLink');
    navigator.clipboard.writeText(input.value).then(() => {
        document.getElementById('copyMsg').classList.remove('hidden');
        setTimeout(() => document.getElementById('copyMsg').classList.add('hidden'), 2000);
    });
}
</script>
@endpush
