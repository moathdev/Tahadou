<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditParticipantRequest;
use App\Http\Requests\RegisterParticipantRequest;
use App\Models\Group;
use App\Models\Participant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

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

        abort_if($group->is_drawn, 422, 'Draw has already been executed.');
        abort_if($group->is_locked, 422, 'Registration is locked.');
        abort_if($group->isFull(), 422, 'Group is full.');

        $editToken = Str::random(48);

        $participant = $group->participants()->create([
            'name'         => $request->name,
            'phone_number' => $request->phone_number,
            'gender'       => $request->gender,
            'interests'    => $request->interests,
            'edit_token'   => $editToken,
        ]);

        return redirect()->route('participant.success', [
            'uuid'       => $uuid,
            'edit_token' => $editToken,
        ]);
    }

    /**
     * Show success page (with edit link).
     */
    public function success(string $uuid)
    {
        $group     = Group::where('uuid', $uuid)->firstOrFail();
        $editToken = request()->query('edit_token');

        // Resolve participant via token if provided
        $participant = $editToken
            ? Participant::where('edit_token', $editToken)->where('group_id', $group->id)->first()
            : null;

        return view('participant.success', compact('group', 'participant', 'editToken'));
    }

    /**
     * Show edit form (requires valid edit_token and draw not yet executed).
     */
    public function editForm(string $uuid, string $editToken)
    {
        $group       = Group::where('uuid', $uuid)->firstOrFail();
        $participant = Participant::where('edit_token', $editToken)
            ->where('group_id', $group->id)
            ->firstOrFail();

        if ($group->is_drawn) {
            return view('participant.closed', ['reason' => 'draw_executed', 'group' => $group]);
        }

        $interests = config('tahadou.interests');

        return view('participant.edit', compact('group', 'participant', 'interests', 'editToken'));
    }

    /**
     * Save edited participant data.
     */
    public function editSave(EditParticipantRequest $request, string $uuid, string $editToken): RedirectResponse
    {
        $group       = Group::where('uuid', $uuid)->firstOrFail();
        $participant = Participant::where('edit_token', $editToken)
            ->where('group_id', $group->id)
            ->firstOrFail();

        abort_if($group->is_drawn, 403, 'Draw already executed — cannot edit registration.');

        $participant->update([
            'name'      => $request->name,
            'gender'    => $request->gender,
            'interests' => $request->interests,
        ]);

        return redirect()->route('participant.success', [
            'uuid'       => $uuid,
            'edit_token' => $editToken,
        ])->with('updated', true);
    }
}
