@extends('layouts.app')

@section('title', 'Admin Dashboard — '. $group->name)

@section('content')
<div class="max-w-3xl mx-auto">

    <!-- Header -->
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">🛠 Admin Dashboard</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $group->name }}</p>
        </div>
        <div class="text-right">
            @if($group->is_drawn)
                <span class="inline-block px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">✅ Draw Executed</span>
            @elseif($group->is_locked)
                <span class="inline-block px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">🔒 Registration Locked</span>
            @else
                <span class="inline-block px-3 py-1 rounded-full bg-violet-100 text-violet-700 text-xs font-semibold">🟢 Registration Open</span>
            @endif
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm text-center">
            <div class="text-3xl font-bold text-violet-600">{{ $group->participants_count }}</div>
            <div class="text-xs text-gray-400 mt-1">Registered</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm text-center">
            <div class="text-3xl font-bold text-amber-500">{{ $group->max_participants }}</div>
            <div class="text-xs text-gray-400 mt-1">Max</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm text-center">
            <div class="text-3xl font-bold text-gray-600">{{ $group->max_participants - $group->participants_count }}</div>
            <div class="text-xs text-gray-400 mt-1">Remaining</div>
        </div>
    </div>

    <!-- Actions -->
    @if(! $group->is_drawn)
    <div class="grid grid-cols-2 gap-4 mb-6">
        <!-- Lock Toggle -->
        <form action="{{ route('admin.lock', $group->uuid) }}" method="POST">
            @csrf
            <button
                type="submit"
                class="w-full py-3 rounded-xl border font-medium text-sm transition
                    {{ $group->is_locked
                        ? 'bg-green-50 border-green-300 text-green-700 hover:bg-green-100'
                        : 'bg-red-50 border-red-300 text-red-700 hover:bg-red-100' }}"
            >
                {{ $group->is_locked ? '🔓 Unlock Registration' : '🔒 Lock Registration' }}
            </button>
        </form>

        <!-- Execute Draw -->
        <form action="{{ route('admin.draw', $group->uuid) }}" method="POST"
              onsubmit="return confirm('Execute the draw? This cannot be undone.');">
            @csrf
            <button
                type="submit"
                {{ $group->participants_count < 3 ? 'disabled' : '' }}
                class="w-full py-3 rounded-xl font-medium text-sm transition
                    {{ $group->participants_count >= 3
                        ? 'bg-violet-600 text-white hover:bg-violet-700 shadow'
                        : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
            >
                🎯 Execute Draw
                @if($group->participants_count < 3)
                    <span class="text-xs">(need {{ 3 - $group->participants_count }} more)</span>
                @endif
            </button>
        </form>
    </div>
    @endif

    @error('draw')
        <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
    @enderror

    <!-- Participants List -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-700">👥 Participants ({{ $group->participants_count }})</h2>
        </div>

        @if($participants->isEmpty())
            <div class="px-6 py-10 text-center text-gray-400 text-sm">
                No participants yet. Share the registration link!
            </div>
        @else
            <ul class="divide-y divide-gray-50">
                @foreach($participants as $participant)
                <li class="flex items-center justify-between px-6 py-4">
                    <div>
                        <p class="font-medium text-gray-700">{{ $participant->name }}</p>
                        <p class="text-xs text-gray-400">Registered {{ $participant->created_at->diffForHumans() }}</p>
                    </div>

                    @if(! $group->is_drawn)
                    <form
                        action="{{ route('admin.participants.remove', [$group->uuid, $participant->id]) }}"
                        method="POST"
                        onsubmit="return confirm('Remove {{ $participant->name }}?');"
                    >
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="text-xs text-red-400 hover:text-red-600 transition"
                        >
                            Remove
                        </button>
                    </form>
                    @endif
                </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Registration Link -->
    <div class="mt-6 bg-violet-50 rounded-xl p-4 border border-violet-100">
        <p class="text-xs text-violet-600 font-medium mb-2">🔗 Registration Link</p>
        <div class="flex items-center gap-2">
            <code class="flex-1 text-xs text-violet-700 bg-white px-3 py-2 rounded-lg border border-violet-200 truncate">
                {{ route('participant.register', $group->uuid) }}
            </code>
            <button
                onclick="navigator.clipboard.writeText('{{ route('participant.register', $group->uuid) }}')"
                class="px-3 py-2 bg-violet-600 text-white rounded-lg text-xs hover:bg-violet-700 transition"
            >
                Copy
            </button>
        </div>
    </div>
</div>
@endsection
