<?php

namespace App\Services;

use App\Jobs\SendWhatsAppNotification;
use App\Models\Group;
use App\Models\Participant;
use Illuminate\Support\Collection;

class DrawService
{
    /**
     * Execute the gift exchange draw for a group.
     *
     * Algorithm: Circular Permutation (Derangement)
     * - Shuffle participants randomly
     * - Shift by 1: participant[i] gives to participant[i+1]
     * - Last participant gives to participant[0]
     * Guarantees: no self-draw, no two-person closed loops (for groups > 2)
     */
    public function execute(Group $group): void
    {
        /** @var Collection<Participant> $participants */
        $participants = $group->participants()->inRandomOrder()->get();
        $count        = $participants->count();

        if ($count < 3) {
            throw new \RuntimeException('Minimum 3 participants required for the draw.');
        }

        // Assign: participant[i] → participant[(i+1) % n]
        for ($i = 0; $i < $count; $i++) {
            $giver    = $participants[$i];
            $receiver = $participants[($i + 1) % $count];

            $giver->update(['assigned_to_id' => $receiver->id]);

            // Dispatch WhatsApp notification to queue
            SendWhatsAppNotification::dispatch($giver->id, $receiver->id)
                ->onQueue('whatsapp');
        }

        $group->update(['is_drawn' => true, 'is_locked' => true]);
    }
}
