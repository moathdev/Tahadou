<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGroupRequest;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class GroupController extends Controller
{
    /**
     * Show landing page — create group form.
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Create a new group then redirect to show page (PRG pattern).
     */
    public function create(CreateGroupRequest $request)
    {
        $rawAdminCode = strtoupper(Str::random(8));

        $group = Group::create([
            'name'             => $request->name,
            'max_participants' => $request->max_participants,
            'max_gift_price'   => $request->max_gift_price ?: null,
            'admin_code'       => Hash::make($rawAdminCode),
            'admin_lookup'     => hash('sha256', strtoupper($rawAdminCode)),
        ]);

        // Flash admin code once — consumed on next request only
        session()->flash('admin_code', $rawAdminCode);

        return redirect()->route('group.show', ['uuid' => $group->uuid]);
    }

    /**
     * Show created group details (GET — safe to refresh).
     */
    public function show(string $uuid)
    {
        $group = Group::where('uuid', $uuid)->firstOrFail();

        return view('group.created', [
            'group'         => $group,
            'shareableLink' => route('participant.register', ['uuid' => $group->uuid]),
            'adminCode'     => session('admin_code'), // null if page was refreshed
        ]);
    }
}
