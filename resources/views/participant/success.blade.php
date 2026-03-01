@extends('layouts.app')

@section('title', 'Registered! — Tahadou')

@section('content')
<div class="max-w-md mx-auto text-center">
    <div class="text-7xl mb-6">🎊</div>
    <h1 class="text-3xl font-bold text-violet-800 mb-3">You're In!</h1>
    <p class="text-gray-600 mb-2">
        You've successfully joined <strong>{{ $group->name }}</strong>.
    </p>
    <p class="text-gray-500 text-sm mb-8">
        On the day of the draw, you'll receive a WhatsApp message with the name of the person you're gifting — along with their interests to help you choose the perfect gift! 🎁
    </p>

    <div class="bg-violet-50 rounded-2xl border border-violet-100 p-6 text-left space-y-3">
        <h2 class="font-semibold text-violet-700 text-sm">📋 What happens next?</h2>
        <ul class="text-sm text-gray-600 space-y-2">
            <li>✅ Your registration is confirmed</li>
            <li>⏳ Wait for the admin to execute the draw</li>
            <li>📱 You'll get a WhatsApp message with your assignment</li>
            <li>🎁 Prepare a thoughtful gift before Eid!</li>
        </ul>
    </div>

    <p class="text-xs text-gray-400 mt-8">عيد مبارك وكل عام وأنتم بخير 🌙</p>
</div>
@endsection
