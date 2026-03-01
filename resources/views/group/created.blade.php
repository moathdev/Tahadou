@extends('layouts.app')

@section('title', 'Group Created — Tahadou')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="text-center mb-8">
        <div class="text-5xl mb-3">🎊</div>
        <h1 class="text-3xl font-bold text-violet-800">Group Created!</h1>
        <p class="text-gray-500 mt-1">Save the details below — you'll need them.</p>
    </div>

    <!-- Shareable Link -->
    <div class="bg-white rounded-2xl shadow-lg border border-violet-100 p-6 mb-5">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">🔗 Registration Link</h2>
        <p class="text-xs text-gray-400 mb-2">Share this link with your group members:</p>
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
                Copy
            </button>
        </div>
        <p id="copyMsg" class="text-green-600 text-xs mt-2 hidden">✅ Copied!</p>
    </div>

    <!-- Admin Code -->
    <div class="bg-amber-50 rounded-2xl border border-amber-200 p-6 mb-5">
        <h2 class="text-sm font-semibold text-amber-700 uppercase tracking-wide mb-1">🔑 Your Admin Code</h2>
        <p class="text-xs text-amber-600 mb-3">⚠️ Save this now — it will <strong>not</strong> be shown again.</p>
        <div class="text-3xl font-mono font-bold text-amber-800 tracking-widest text-center py-3 bg-white rounded-xl border border-amber-200">
            {{ $adminCode }}
        </div>
    </div>

    <!-- Admin Dashboard Link -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">🛠 Admin Dashboard</h2>
        <a
            href="{{ route('admin.login', $group->uuid) }}"
            class="block w-full text-center py-3 rounded-xl bg-gray-800 text-white text-sm font-medium hover:bg-gray-900 transition"
        >
            Go to Dashboard →
        </a>
    </div>

    <p class="text-center text-xs text-gray-400">
        Group: <strong>{{ $group->name }}</strong> · Max participants: <strong>{{ $group->max_participants }}</strong>
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
