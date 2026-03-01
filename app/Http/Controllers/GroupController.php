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
     * Create a new group and return the shareable link + admin code.
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

        return view('group.created', [
            'group'          => $group,
            'shareableLink'  => route('participant.register', ['uuid' => $group->uuid]),
            'adminCode'      => $rawAdminCode,
        ]);
    }
}
