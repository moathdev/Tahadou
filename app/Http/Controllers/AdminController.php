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
     * Show the "enter admin code" page (entry point from navbar).
     */
    public function findForm()
    {
        return view('admin.find');
    }

    /**
     * Look up group by admin code (via SHA-256 lookup) and authenticate in one step.
     */
    public function findAndLogin(Request $request): RedirectResponse
    {
        $input = strtoupper(trim($request->input('admin_code', '')));

        if (empty($input)) {
            return back()->withErrors(['admin_code' => __('app.admin_find_uuid_required')]);
        }

        $lookup = hash('sha256', $input);
        $group  = Group::where('admin_lookup', $lookup)->first();

        if (! $group || ! Hash::check($input, $group->admin_code)) {
            return back()->withErrors(['admin_code' => __('app.admin_code_invalid')]);
        }

        Session::put("admin_group_{$group->uuid}", true);

        return redirect()->route('admin.dashboard', $group->uuid);
    }

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

        return redirect()->route('admin.dashboard', $uuid)->with('success', __('app.participant_removed'));
    }

    /**
     * Toggle registration lock.
     */
    public function toggleLock(string $uuid): RedirectResponse
    {
        $this->requireAuth($uuid);

        $group = Group::where('uuid', $uuid)->firstOrFail();
        $group->update(['is_locked' => ! $group->is_locked]);

        $msg = $group->is_locked ? __('app.registration_locked') : __('app.registration_unlocked');
        return redirect()->route('admin.dashboard', $uuid)->with('success', $msg);
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

        return redirect()->route('admin.dashboard', $uuid)->with('success', __('app.draw_success'));
    }

    /**
     * Download draw results as XLSX (RTL, WhatsApp links, message text).
     */
    public function downloadExcel(string $uuid): \Illuminate\Http\Response
    {
        $this->requireAuth($uuid);

        $group = Group::where('uuid', $uuid)->firstOrFail();

        abort_unless($group->is_drawn, 403, 'Draw has not been executed yet.');

        $participants = $group->participants()->with('assignedTo')->get();

        $xlsx = new \App\Services\XlsxExporter();

        // Header row — bold
        $xlsx->addRow(array_map(
            fn ($h) => ['value' => $h, 'bold' => true],
            ['المهدي', 'رقم الجوال', 'الاهتمامات', 'سيهدي إلى', 'جوال المهدى إليه', 'اضغط وأرسل', 'النص المرسل']
        ));

        foreach ($participants as $p) {
            $receiver = $p->assignedTo;

            // Interests — no emojis
            $interests = collect($p->interests ?? [])
                ->map(fn ($key) => $this->stripEmoji(__("app.interest_{$key}")))
                ->implode(' | ');

            // Receiver interests for the message
            $receiverInterests = collect($receiver?->interests ?? [])
                ->map(fn ($key) => $this->stripEmoji(__("app.interest_{$key}")))
                ->implode("\n- ");

            // WhatsApp phone (international, no +)
            $waPhone = $this->toWaPhone($p->phone_number);

            // Message text
            $message = $this->buildMessage($group->name, $p->name, $receiver?->name, $receiverInterests);

            // WhatsApp URL
            $waUrl = 'https://api.whatsapp.com/send?phone=' . $waPhone
                . '&text=' . rawurlencode($message);

            $xlsx->addRow([
                $p->name,
                $p->phone_number,
                $interests,
                $receiver?->name ?? '—',
                $receiver?->phone_number ?? '—',
                ['value' => 'ارسل رسالة', 'url' => $waUrl],
                $message,
            ]);
        }

        $content  = $xlsx->generate();
        $filename = 'tahadou-draw-' . now()->format('Y-m-d') . '.xls';

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length'      => strlen($content),
            'Cache-Control'       => 'no-store, no-cache',
            'Pragma'              => 'no-cache',
        ]);
    }

    /** Format phone for WhatsApp API (966XXXXXXXXX) */
    private function toWaPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '00')) {
            return substr($digits, 2);
        }
        if (str_starts_with($digits, '0')) {
            return '966' . substr($digits, 1);
        }
        return $digits;
    }

    /** Strip emoji/symbols from a string */
    private function stripEmoji(string $text): string
    {
        return trim(preg_replace('/[\x{1F000}-\x{1FFFF}]|[\x{2600}-\x{27FF}]|\x{FE0F}/u', '', $text));
    }

    /** Build the WhatsApp message text */
    private function buildMessage(string $groupName, string $giverName, ?string $receiverName, string $receiverInterests): string
    {
        $receiver  = $receiverName  ?? '—';
        $interests = $receiverInterests ?: '—';

        $lines = [
            "مرحباً {$giverName}،",
            "أنت ضمن قرعة \"{$groupName}\" لتبادل الهدايا 🎁",
            '',
            'الشخص الذي ستهديه:',
            $receiver,
            '',
            'اهتماماته:',
            "- {$interests}",
            '',
            'جهّز له هدية قبل العيد 🌙',
        ];

        return implode("\n", $lines);
    }

    /**
     * Verify admin session for this group.
     */
    private function requireAuth(string $uuid): void
    {
        abort_unless(Session::get("admin_group_{$uuid}"), 403, 'Unauthorized. Please login.');
    }
}
