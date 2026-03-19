@extends('layouts.app')

@section('title', __('app.edit_title') . ' — ' . $group->name)

@section('content')
<div class="max-w-lg mx-auto">

    <!-- Header -->
    <div class="text-center mb-8">
        <div class="text-5xl mb-3">✏️</div>
        <h1 class="text-2xl font-bold text-gray-800">{{ __('app.edit_title') }}</h1>
        <p class="text-violet-600 font-medium mt-1">{{ $group->name }}</p>
        <p class="text-gray-400 text-xs mt-1">{{ __('app.edit_subtitle') }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-violet-100 p-8">
        <form action="{{ route('participant.edit.submit', ['uuid' => $group->uuid, 'editToken' => $editToken]) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Full Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-600 mb-1">
                    {{ __('app.name_label') }} <span class="text-red-400">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $participant->name) }}"
                    placeholder="{{ __('app.name_placeholder') }}"
                    required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-violet-400 focus:border-violet-400 outline-none transition text-sm"
                />
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Gender -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">
                    {{ __('app.gender_label') }} <span class="text-red-400">*</span>
                </label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['male' => '👨', 'female' => '👩', 'child' => '🧒'] as $value => $emoji)
                    @php $selected = old('gender', $participant->gender) === $value; @endphp
                    <label class="flex flex-col items-center gap-1.5 p-3 rounded-xl border cursor-pointer hover:border-violet-300 hover:bg-violet-50 transition gender-option {{ $selected ? 'border-violet-400 bg-violet-50 ring-2 ring-violet-300' : 'border-gray-200' }}">
                        <input
                            type="radio"
                            name="gender"
                            value="{{ $value }}"
                            {{ $selected ? 'checked' : '' }}
                            class="sr-only gender-radio"
                            required
                        />
                        <span class="text-2xl">{{ $emoji }}</span>
                        <span class="text-xs font-medium text-gray-600">{{ __('app.gender_' . $value) }}</span>
                    </label>
                    @endforeach
                </div>
                @error('gender')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Gift Interests -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    {{ __('app.interests_label') }} <span class="text-red-400">*</span>
                    <span class="text-gray-400 font-normal">{{ __('app.interests_hint_count') }}</span>
                </label>
                <p class="text-xs text-gray-400 mb-3">{{ __('app.interests_hint') }}</p>

                <div class="grid grid-cols-2 gap-2" id="interests-grid">
                    @foreach($interests as $key)
                    @php $checked = in_array($key, old('interests', $participant->interests ?? [])); @endphp
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer hover:border-violet-300 hover:bg-violet-50 transition interest-option {{ $checked ? 'border-violet-400 bg-violet-50' : '' }}">
                        <input
                            type="checkbox"
                            name="interests[]"
                            value="{{ $key }}"
                            {{ $checked ? 'checked' : '' }}
                            class="interest-checkbox accent-violet-600"
                        />
                        <span class="text-sm">{{ __('app.interest_' . $key) }}</span>
                    </label>
                    @endforeach
                </div>

                @error('interests')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror

                <p id="interest-warning" class="text-amber-500 text-xs mt-2 hidden">
                    {{ __('app.interests_max_warn') }}
                </p>
            </div>

            <div class="flex gap-3">
                <a
                    href="{{ route('participant.success', ['uuid' => $group->uuid, 'edit_token' => $editToken]) }}"
                    class="flex-1 py-3 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 font-medium text-sm text-center transition"
                >
                    {{ __('app.cancel_btn') }}
                </a>
                <button
                    type="submit"
                    class="flex-1 py-3 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold transition text-sm shadow"
                >
                    {{ __('app.edit_save_btn') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Gender selection highlight
    document.querySelectorAll('.gender-radio').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.gender-option').forEach(label => {
                label.classList.remove('border-violet-400', 'bg-violet-50', 'ring-2', 'ring-violet-300');
                label.classList.add('border-gray-200');
            });
            if (radio.checked) {
                const lbl = radio.closest('label');
                lbl.classList.add('border-violet-400', 'bg-violet-50', 'ring-2', 'ring-violet-300');
                lbl.classList.remove('border-gray-200');
            }
        });
    });

    // Interests selection highlight
    const checkboxes = document.querySelectorAll('.interest-checkbox');
    const warning    = document.getElementById('interest-warning');

    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            const checked = document.querySelectorAll('.interest-checkbox:checked');
            if (checked.length > 3) {
                cb.checked = false;
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
            checkboxes.forEach(box => {
                box.closest('label').classList.toggle('border-violet-400', box.checked);
                box.closest('label').classList.toggle('bg-violet-50', box.checked);
            });
        });
    });
</script>
@endpush
