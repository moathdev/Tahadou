@extends('layouts.app')

@section('title', __('app.dashboard_title') . ' — ' . $group->name)

@section('content')
<div class="max-w-3xl mx-auto">

    <!-- Header -->
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('app.dashboard_title') }}</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $group->name }}</p>
        </div>
        <div class="text-right">
            @if($group->is_drawn)
                <span class="inline-block px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">{{ __('app.status_draw_done') }}</span>
            @elseif($group->is_locked)
                <span class="inline-block px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">{{ __('app.status_locked') }}</span>
            @else
                <span class="inline-block px-3 py-1 rounded-full bg-violet-100 text-violet-700 text-xs font-semibold">{{ __('app.status_open') }}</span>
            @endif
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm text-center">
            <div class="text-3xl font-bold text-violet-600">{{ $group->participants_count }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ __('app.stat_registered') }}</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm text-center">
            <div class="text-3xl font-bold text-amber-500">{{ $group->max_participants }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ __('app.stat_max') }}</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm text-center">
            <div class="text-3xl font-bold text-gray-600">{{ $group->max_participants - $group->participants_count }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ __('app.stat_remaining') }}</div>
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
                {{ $group->is_locked ? __('app.btn_unlock') : __('app.btn_lock') }}
            </button>
        </form>

        <!-- Execute Draw -->
        <form
            action="{{ route('admin.draw', $group->uuid) }}"
            method="POST"
            onsubmit="return confirm('{{ __('app.draw_confirm') }}');"
        >
            @csrf
            <button
                type="submit"
                {{ $group->participants_count < 3 ? 'disabled' : '' }}
                class="w-full py-3 rounded-xl font-medium text-sm transition
                    {{ $group->participants_count >= 3
                        ? 'bg-violet-600 text-white hover:bg-violet-700 shadow'
                        : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
            >
                {{ __('app.btn_draw') }}
                @if($group->participants_count < 3)
                    <span class="text-xs">{{ __('app.btn_draw_need_more', ['count' => 3 - $group->participants_count]) }}</span>
                @endif
            </button>
        </form>
    </div>
    @endif

    @error('draw')
        <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
    @enderror

    {{-- Download Excel — shown only after draw --}}
    @if($group->is_drawn)
    <div class="mb-6">
        <a
            href="{{ route('admin.download.excel', $group->uuid) }}"
            class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold text-sm shadow transition"
        >
            {{ __('app.btn_download_excel') }}
        </a>
    </div>
    @endif

    <!-- Participants List -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-700">{{ __('app.participants_heading', ['count' => $group->participants_count]) }}</h2>
        </div>

        @if($participants->isEmpty())
            <div class="px-6 py-10 text-center text-gray-400 text-sm">
                {{ __('app.no_participants') }}
            </div>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach($participants as $participant)
                <li class="px-6 py-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">

                            {{-- Name + time --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-semibold text-gray-800">{{ $participant->name }}</p>
                                @if($group->is_drawn && $participant->assignedTo)
                                    <span class="text-xs px-2 py-0.5 bg-violet-100 text-violet-700 rounded-full font-medium">
                                        🎁 → {{ $participant->assignedTo->name }}
                                    </span>
                                @endif
                            </div>

                            {{-- Phone --}}
                            <p class="text-xs text-gray-500 mt-1">
                                📱 {{ $participant->phone_number }}
                            </p>

                            {{-- Interests --}}
                            @if($participant->interests && count($participant->interests))
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($participant->interests as $interestKey)
                                    <span class="text-xs px-2 py-0.5 bg-amber-50 border border-amber-200 text-amber-700 rounded-full">
                                        {{ __('app.interest_' . $interestKey) }}
                                    </span>
                                @endforeach
                            </div>
                            @endif

                            <p class="text-xs text-gray-400 mt-2">
                                {{ __('app.registered_at', ['time' => $participant->created_at->diffForHumans()]) }}
                            </p>
                        </div>

                        {{-- Remove button --}}
                        @if(! $group->is_drawn)
                        <form
                            action="{{ route('admin.participants.remove', [$group->uuid, $participant->id]) }}"
                            method="POST"
                            onsubmit="return confirm('{{ __('app.remove_confirm', ['name' => $participant->name]) }}');"
                            class="shrink-0"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition mt-1">
                                {{ __('app.remove_btn') }}
                            </button>
                        </form>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Registration Link -->
    <div class="mt-6 bg-violet-50 rounded-xl p-4 border border-violet-100">
        <p class="text-xs text-violet-600 font-medium mb-2">{{ __('app.reg_link_label') }}</p>
        <div class="flex items-center gap-2">
            <code class="flex-1 text-xs text-violet-700 bg-white px-3 py-2 rounded-lg border border-violet-200 truncate">
                {{ route('participant.register', $group->uuid) }}
            </code>
            <button
                onclick="navigator.clipboard.writeText('{{ route('participant.register', $group->uuid) }}')"
                class="px-3 py-2 bg-violet-600 text-white rounded-lg text-xs hover:bg-violet-700 transition"
            >
                {{ __('app.copy_btn') }}
            </button>
        </div>
    </div>
</div>
@endsection
