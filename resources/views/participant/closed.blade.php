@extends('layouts.app')

@section('title', 'Registration Closed — Tahadou')

@section('content')
<div class="max-w-md mx-auto text-center">
    @if($reason === 'draw_executed')
        <div class="text-6xl mb-4">✅</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Draw Already Executed</h1>
        <p class="text-gray-500">The gift exchange draw for <strong>{{ $group->name }}</strong> has already been completed. Check your WhatsApp for your assignment!</p>

    @elseif($reason === 'locked')
        <div class="text-6xl mb-4">🔒</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Registration Locked</h1>
        <p class="text-gray-500">The admin has locked registration for <strong>{{ $group->name }}</strong>. Please contact the group admin.</p>

    @elseif($reason === 'full')
        <div class="text-6xl mb-4">🈵</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Group is Full</h1>
        <p class="text-gray-500"><strong>{{ $group->name }}</strong> has reached its maximum capacity of {{ $group->max_participants }} participants.</p>
    @endif

    <a href="{{ route('home') }}" class="mt-8 inline-block px-6 py-3 rounded-xl bg-violet-600 text-white text-sm font-medium hover:bg-violet-700 transition">
        Create Your Own Group →
    </a>
</div>
@endsection
