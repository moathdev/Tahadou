<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Models\Group;
use App\Models\Participant;
use App\Services\DrawService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function __construct(protected DrawService $drawService) {}

    /**
     * Show admin login form.
     */
    public function loginForm(string $uuid)
    {
        $group = Group::where('uuid', $uuid)->firstOrFail();
        return view('admin.login', compact('group'));
    }

    /**
     * Authenticate admin with the group admin code.
     */
    public function login(AdminLoginRequest $request, string $uuid): RedirectResponse
    {
        $group = Group::where('uuid', $uuid)->firstOrFail();

        if (! Hash::check($request->admin_code, $group->admin_code)) {
            return back()->withErrors(['admin_code' => 'Invalid admin code.']);
        }

        Session::put("admin_group_{$group->uuid}", true);

        return redirect()->route('admin.dashboard', $uuid);
    }

    /**
     * Show admin dashboard.
     */
    public function dashboard(string $uuid)
    {
        $this->requireAuth($uuid);

        $group        = Group::where('uuid', $uuid)->withCount('participants')->firstOrFail();
        $participants = $group->participants()
            ->with('assignedTo')
            ->select(['id', 'name', 'phone_number', 'interests', 'assigned_to_id', 'created_at'])
            ->latest()
            ->get();

        return view('admin.dashboard', compact('group', 'participants'));
    }

    /**
     * Remove a participant.
     */
    public function removeParticipant(string $uuid, Participant $participant): RedirectResponse
    {
        $this->requireAuth($uuid);

        $group = Group::where('uuid', $uuid)->firstOrFail();

        abort_unless($participant->group_id === $group->id, 403);
        abort_if($group->is_drawn, 403, 'Draw already executed — cannot remove participants.');

        $participant->delete();

        return redirect()->route('admin.dashboard', $uuid)->with('success', 'Participant removed.');
    }

    /**
     * Toggle registration lock.
     */
    public function toggleLock(string $uuid): RedirectResponse
    {
        $this->requireAuth($uuid);

        $group = Group::where('uuid', $uuid)->firstOrFail();
        $group->update(['is_locked' => ! $group->is_locked]);

        $status = $group->is_locked ? 'locked' : 'unlocked';
        return redirect()->route('admin.dashboard', $uuid)->with('success', "Registration {$status}.");
    }

    /**
     * Execute the draw.
     */
    public function executeDraw(string $uuid): RedirectResponse
    {
        $this->requireAuth($uuid);

        $group = Group::where('uuid', $uuid)->firstOrFail();

        if ($group->is_drawn) {
            return back()->withErrors(['draw' => 'Draw already executed.']);
        }

        if ($group->participants()->count() < 3) {
            return back()->withErrors(['draw' => 'Minimum 3 participants required to execute the draw.']);
        }

        $this->drawService->execute($group);

        return redirect()->route('admin.dashboard', $uuid)->with('success', '🎁 Draw executed! WhatsApp messages are being sent.');
    }

    /**
     * Verify admin session for this group.
     */
    private function requireAuth(string $uuid): void
    {
        abort_unless(Session::get("admin_group_{$uuid}"), 403, 'Unauthorized. Please login.');
    }
}
