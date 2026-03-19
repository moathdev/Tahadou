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
            <div class="text-xs text-gray-400 mt-1">{{ __('app.stat_participants') }}</div>
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

    {{-- Max gift price reminder --}}
    @if($group->max_gift_price)
    <div class="mb-5 flex items-center gap-3 px-5 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm font-medium">
        {{ __('app.max_gift_price_badge', ['price' => number_format($group->max_gift_price)]) }}
    </div>
    @endif

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
                    <div class="flex items-center gap-3">

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-semibold text-gray-800">
                                    @if($participant->gender === 'male') 👨
                                    @elseif($participant->gender === 'female') 👩
                                    @elseif($participant->gender === 'child') 🧒
                                    @endif
                                    {{ $participant->name }}
                                </p>
                                <span class="text-xs px-2 py-0.5 rounded-full
                                    {{ $participant->gender === 'male' ? 'bg-blue-50 text-blue-600' : ($participant->gender === 'female' ? 'bg-pink-50 text-pink-600' : 'bg-orange-50 text-orange-600') }}">
                                    {{ __('app.gender_' . ($participant->gender ?? 'male')) }}
                                </span>
                                @if($group->is_drawn && $participant->assignedTo)
                                    <span dir="ltr" class="text-xs px-2 py-0.5 bg-violet-100 text-violet-700 rounded-full font-medium">
                                        🎁 → {{ $participant->assignedTo->name }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                📱 <span dir="ltr">{{ $participant->phone_number }}</span>
                            </p>
                            @if($participant->interests && count($participant->interests))
                            <div class="flex flex-wrap gap-1 mt-1.5">
                                @foreach($participant->interests as $interestKey)
                                    <span class="text-xs px-2 py-0.5 bg-amber-50 border border-amber-200 text-amber-700 rounded-full">
                                        {{ __('app.interest_' . $interestKey) }}
                                    </span>
                                @endforeach
                            </div>
                            @endif
                            <p class="text-xs text-gray-400 mt-1.5">
                                {{ __('app.registered_at', ['time' => $participant->created_at->diffForHumans()]) }}
                            </p>
                        </div>

                        {{-- WhatsApp send button — ms-auto: left in Arabic, right in English --}}
                        @if($group->is_drawn && $participant->assignedTo)
                        @php
                            $digits   = preg_replace('/\D/', '', $participant->phone_number);
                            $waPhone  = str_starts_with($digits, '0') ? '966' . substr($digits, 1) : $digits;

                            $receiverInterests = collect($participant->assignedTo->interests ?? [])
                                ->map(fn($k) => strip_tags(__("app.interest_{$k}")))
                                ->implode("\n- ");

                            $priceNote = $group->max_gift_price
                                ? __('app.wa_price_note', ['price' => number_format($group->max_gift_price)])
                                : null;

                            $lines = [
                                __('app.wa_greeting', ['name' => $participant->name]),
                                __('app.wa_intro', ['group' => $group->name]),
                                "",
                                __('app.wa_assigned_label'),
                                $participant->assignedTo->name,
                                "",
                                __('app.wa_interests_label'),
                                "- {$receiverInterests}",
                                "",
                            ];
                            if ($priceNote) $lines[] = $priceNote;
                            $lines[] = __('app.wa_closing');

                            $waMsg = implode("\n", $lines);

                            $waUrl = 'https://api.whatsapp.com/send?phone=' . $waPhone . '&text=' . rawurlencode($waMsg);
                        @endphp
                        <a
                            href="{{ $waUrl }}"
                            target="_blank"
                            data-participant-id="{{ $participant->id }}"
                            data-label-default="{{ __('app.btn_send_whatsapp') }}"
                            data-label-sent="{{ __('app.btn_sent_whatsapp') }}"
                            data-label-resend="{{ __('app.btn_resend_whatsapp') }}"
                            class="wa-send-btn ms-auto shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-green-500 hover:bg-green-600 text-white text-xs font-semibold transition"
                        >
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            <span class="btn-label">{{ __('app.btn_send_whatsapp') }}</span>
                        </a>
                        @endif

                        {{-- Edit + Remove buttons (admin only, before draw) --}}
                        @if(! $group->is_drawn)
                        <div class="ms-auto shrink-0 flex items-center gap-2">
                            <button
                                type="button"
                                onclick="openEditModal({{ $participant->id }}, '{{ addslashes($participant->name) }}', '{{ $participant->gender }}', {{ json_encode($participant->interests ?? []) }})"
                                class="text-xs text-violet-500 hover:text-violet-700 transition font-medium"
                            >
                                {{ __('app.btn_edit_participant') }}
                            </button>
                            <form
                                action="{{ route('admin.participants.remove', [$group->uuid, $participant->id]) }}"
                                method="POST"
                                onsubmit="return confirm('{{ __('app.remove_confirm', ['name' => addslashes($participant->name)]) }}');"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition">
                                    {{ __('app.remove_btn') }}
                                </button>
                            </form>
                        </div>
                        @endif

                    </div>
                </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Edit Participant Modal -->
    @if(! $group->is_drawn)
    <div id="edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 w-full max-w-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-5">{{ __('app.edit_participant_title') }}</h2>

            <form id="edit-form" method="POST" class="space-y-5">
                @csrf
                @method('PATCH')

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        {{ __('app.name_label') }} <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="edit-name"
                        required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-violet-400 focus:border-violet-400 outline-none transition text-sm"
                    />
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">
                        {{ __('app.gender_label') }} <span class="text-red-400">*</span>
                    </label>
                    <input type="hidden" name="gender" id="modal-gender-value" value="" />
                    <div class="grid grid-cols-3 gap-3">
                        @foreach(['male' => '👨', 'female' => '👩', 'child' => '🧒'] as $gVal => $gEmoji)
                        <button
                            type="button"
                            data-gender="{{ $gVal }}"
                            class="modal-gender-btn flex flex-col items-center gap-1.5 p-3 rounded-xl border border-gray-200 cursor-pointer hover:border-violet-300 hover:bg-violet-50 transition"
                        >
                            <span class="text-2xl">{{ $gEmoji }}</span>
                            <span class="text-xs font-medium text-gray-600">{{ __('app.gender_' . $gVal) }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Interests -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        {{ __('app.interests_label') }}
                        <span class="text-gray-400 font-normal">{{ __('app.interests_hint_count') }}</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(config('tahadou.interests') as $iKey)
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer hover:border-violet-300 hover:bg-violet-50 transition modal-interest-option">
                            <input type="checkbox" name="interests[]" value="{{ $iKey }}" class="modal-interest-checkbox accent-violet-600" />
                            <span class="text-xs">{{ __('app.interest_' . $iKey) }}</span>
                        </label>
                        @endforeach
                    </div>
                    <p id="modal-interest-warning" class="text-amber-500 text-xs mt-2 hidden">
                        {{ __('app.interests_max_warn') }}
                    </p>
                </div>

                <div class="flex gap-3 pt-1">
                    <button
                        type="button"
                        onclick="closeEditModal()"
                        class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium transition"
                    >
                        {{ __('app.cancel_btn') }}
                    </button>
                    <button
                        type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition shadow"
                    >
                        {{ __('app.edit_save_btn') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

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

@push('scripts')
@if(! $group->is_drawn)
<script>
const BASE_EDIT_URL = '{{ rtrim(route("admin.participants.remove", [$group->uuid, 0]), "/0") }}/';

function openEditModal(id, name, gender, interests) {
    const modal = document.getElementById('edit-modal');
    const form  = document.getElementById('edit-form');

    // Set form action
    form.action = BASE_EDIT_URL + id;

    // Populate name
    document.getElementById('edit-name').value = name;

    // Set gender
    document.getElementById('modal-gender-value').value = gender;
    document.querySelectorAll('.modal-gender-btn').forEach(btn => {
        const active = btn.dataset.gender === gender;
        btn.classList.toggle('border-violet-400', active);
        btn.classList.toggle('bg-violet-50', active);
        btn.classList.toggle('ring-2', active);
        btn.classList.toggle('ring-violet-300', active);
        btn.classList.toggle('border-gray-200', !active);
    });

    // Set interests checkboxes
    document.querySelectorAll('.modal-interest-checkbox').forEach(cb => {
        cb.checked = interests.includes(cb.value);
        cb.closest('label').classList.toggle('border-violet-400', cb.checked);
        cb.closest('label').classList.toggle('bg-violet-50', cb.checked);
    });

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditModal() {
    const modal = document.getElementById('edit-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close on backdrop click
document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// Gender button highlight
document.querySelectorAll('.modal-gender-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.modal-gender-btn').forEach(b => {
            b.classList.remove('border-violet-400', 'bg-violet-50', 'ring-2', 'ring-violet-300');
            b.classList.add('border-gray-200');
        });
        btn.classList.add('border-violet-400', 'bg-violet-50', 'ring-2', 'ring-violet-300');
        btn.classList.remove('border-gray-200');
        document.getElementById('modal-gender-value').value = btn.dataset.gender;
    });
});

// Interests max-3 enforcement
document.querySelectorAll('.modal-interest-checkbox').forEach(cb => {
    cb.addEventListener('change', () => {
        const checked = document.querySelectorAll('.modal-interest-checkbox:checked');
        if (checked.length > 3) {
            cb.checked = false;
            document.getElementById('modal-interest-warning').classList.remove('hidden');
        } else {
            document.getElementById('modal-interest-warning').classList.add('hidden');
        }
        document.querySelectorAll('.modal-interest-checkbox').forEach(box => {
            box.closest('label').classList.toggle('border-violet-400', box.checked);
            box.closest('label').classList.toggle('bg-violet-50', box.checked);
        });
    });
});
</script>
@endif

@if($group->is_drawn)
<script>
(function () {
    const STORAGE_KEY = 'wa_sent_{{ $group->uuid }}';

    function getSent() {
        return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    }

    function saveSent(arr) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(arr));
    }

    function markSent(btn) {
        btn.classList.remove('bg-green-500', 'hover:bg-green-600');
        btn.classList.add('bg-gray-200', 'text-gray-500', 'pointer-events-none', 'cursor-default');
        btn.querySelector('.btn-label').textContent = btn.dataset.labelSent;
        btn.querySelector('svg').style.opacity = '0.4';

        // Add resend button if not already there
        if (!btn.nextElementSibling || !btn.nextElementSibling.classList.contains('wa-resend-btn')) {
            const resend = document.createElement('button');
            resend.type = 'button';
            resend.className = 'wa-resend-btn ms-1 shrink-0 px-2 py-1.5 rounded-lg border border-gray-300 text-gray-500 hover:border-green-400 hover:text-green-600 text-xs transition';
            resend.textContent = btn.dataset.labelResend;
            resend.addEventListener('click', function () {
                resetBtn(btn);
            });
            btn.insertAdjacentElement('afterend', resend);
        }
    }

    function resetBtn(btn) {
        // Remove sent state visually
        btn.classList.add('bg-green-500', 'hover:bg-green-600');
        btn.classList.remove('bg-gray-200', 'text-gray-500', 'pointer-events-none', 'cursor-default');
        btn.querySelector('.btn-label').textContent = btn.dataset.labelDefault;
        btn.querySelector('svg').style.opacity = '1';

        // Remove resend button
        const resend = btn.nextElementSibling;
        if (resend && resend.classList.contains('wa-resend-btn')) resend.remove();

        // Remove from localStorage
        const sent = getSent().filter(id => id !== btn.dataset.participantId);
        saveSent(sent);
    }

    // Restore sent state on page load
    getSent().forEach(function (id) {
        const btn = document.querySelector('[data-participant-id="' + id + '"]');
        if (btn) markSent(btn);
    });

    // Handle click — mark as sent + save
    document.querySelectorAll('.wa-send-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id   = this.dataset.participantId;
            const sent = getSent();
            if (!sent.includes(id)) {
                sent.push(id);
                saveSent(sent);
            }
            markSent(this);
        });
    });
})();
</script>
@endif
@endpush
