@extends('layouts.app')

@section('title', 'Admin Login — Tahadou')

@section('content')
<div class="max-w-sm mx-auto">
    <div class="text-center mb-8">
        <div class="text-4xl mb-3">🔑</div>
        <h1 class="text-2xl font-bold text-gray-800">Admin Access</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $group->name }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <form action="{{ route('admin.login.submit', $group->uuid) }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="admin_code" class="block text-sm font-medium text-gray-600 mb-1">
                    Admin Code
                </label>
                <input
                    type="text"
                    id="admin_code"
                    name="admin_code"
                    placeholder="Enter your admin code"
                    required
                    autofocus
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-violet-400 outline-none transition text-sm font-mono uppercase tracking-widest text-center"
                />
                @error('admin_code')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full py-3 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold transition text-sm"
            >
                Access Dashboard →
            </button>
        </form>
    </div>
</div>
@endsection
