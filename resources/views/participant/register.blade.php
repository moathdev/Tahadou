@extends('layouts.app')

@section('title', __('app.join_title') . ' — ' . $group->name)

@section('content')
<div class="max-w-lg mx-auto">

    <!-- Header -->
    <div class="text-center mb-8">
        <div class="text-5xl mb-3">🎁</div>
        <h1 class="text-2xl font-bold text-gray-800">{{ __('app.join_title') }}</h1>
        <p class="text-violet-600 font-medium mt-1">{{ $group->name }}</p>
        <p class="text-gray-400 text-xs mt-1">
            {{ __('app.join_count', ['current' => $group->participants()->count(), 'max' => $group->max_participants]) }}
        </p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-violet-100 p-8">
        <form action="{{ route('participant.register.submit', $group->uuid) }}" method="POST" class="space-y-6">
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
                    value="{{ old('name') }}"
                    placeholder="{{ __('app.name_placeholder') }}"
                    required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-violet-400 focus:border-violet-400 outline-none transition text-sm"
                />
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-600 mb-1">
                    {{ __('app.phone_label') }} <span class="text-red-400">*</span>
                </label>
                <input
                    type="tel"
                    id="phone_number"
                    name="phone_number"
                    value="{{ old('phone_number') }}"
                    placeholder="{{ __('app.phone_placeholder') }}"
                    required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-violet-400 focus:border-violet-400 outline-none transition text-sm"
                />
                <p class="text-xs text-gray-400 mt-1">{{ __('app.phone_hint') }}</p>
                @error('phone_number')
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
                    @foreach($interests as $key => $label)
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer hover:border-violet-300 hover:bg-violet-50 transition interest-option {{ in_array($key, old('interests', [])) ? 'border-violet-400 bg-violet-50' : '' }}">
                        <input
                            type="checkbox"
                            name="interests[]"
                            value="{{ $key }}"
                            {{ in_array($key, old('interests', [])) ? 'checked' : '' }}
                            class="interest-checkbox accent-violet-600"
                        />
                        <span class="text-sm">{{ $label }}</span>
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

            <button
                type="submit"
                class="w-full py-3 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold transition text-sm shadow"
            >
                {{ __('app.join_btn') }}
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
