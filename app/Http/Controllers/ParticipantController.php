<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterParticipantRequest;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;

class ParticipantController extends Controller
{
    /**
     * Show the registration form.
     */
    public function show(string $uuid)
    {
        $group = Group::where('uuid', $uuid)->firstOrFail();

        if ($group->is_drawn) {
            return view('participant.closed', ['reason' => 'draw_executed', 'group' => $group]);
        }

        if ($group->is_locked) {
            return view('participant.closed', ['reason' => 'locked', 'group' => $group]);
        }

        if ($group->isFull()) {
            return view('participant.closed', ['reason' => 'full', 'group' => $group]);
        }

        $interests = config('tahadou.interests');

        return view('participant.register', compact('group', 'interests'));
    }

    /**
     * Register a participant.
     */
    public function register(RegisterParticipantRequest $request, string $uuid): RedirectResponse
    {
        $group = Group::where('uuid', $uuid)->firstOrFail();

        // Re-validate group state
        abort_if($group->is_drawn, 422, 'Draw has already been executed.');
        abort_if($group->is_locked, 422, 'Registration is locked.');
        abort_if($group->isFull(), 422, 'Group is full.');

        $group->participants()->create([
            'name'         => $request->name,
            'phone_number' => $request->phone_number,
            'interests'    => $request->interests,
        ]);

        return redirect()->route('participant.success', $uuid);
    }

    /**
     * Show success page.
     */
    public function success(string $uuid)
    {
        $group = Group::where('uuid', $uuid)->firstOrFail();
        return view('participant.success', compact('group'));
    }
}
