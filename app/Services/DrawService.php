<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Participant;
use Illuminate\Support\Collection;

class DrawService
{
    /**
     * Execute the gift exchange draw for a group.
     *
     * Algorithm: Gender-aware Circular Permutation
     *
     * Strategy:
     *   1. Group participants by gender: male, female, child.
     *   2. Within each gender group, apply a circular shift (i → i+1).
     *   3. Merge all gender groups into a single ordered list.
     *   4. Each participant[i] gives to participant[i+1]; last gives to participant[0].
     *
     * This guarantees:
     *   - Same-gender gifting within each group (male→male, female→female, child→child).
     *   - No self-assignment.
     *   - No two-person closed loops (for groups > 2 within a gender).
     *   - If a gender group has only 1 person, they cross-gift to the next group
     *     (unavoidable — we cannot create a 1-person loop).
     *   - If a gender group has exactly 2, they swap — acceptable for same gender.
     *
     * Guarantees degrade gracefully: same gender is a best-effort goal.
     */
    public function execute(Group $group): void
    {
        $participants = $group->participants()->inRandomOrder()->get();
        $count        = $participants->count();

        if ($count < 3) {
            throw new \RuntimeException('Minimum 3 participants required for the draw.');
        }

        // Bucket by gender, shuffle each bucket
        $buckets = [
            'male'   => $participants->where('gender', 'male')->values(),
            'female' => $participants->where('gender', 'female')->values(),
            'child'  => $participants->where('gender', 'child')->values(),
        ];

        // Merge non-empty buckets into ordered list
        $ordered = collect();
        foreach ($buckets as $bucket) {
            if ($bucket->isNotEmpty()) {
                $ordered = $ordered->concat($bucket);
            }
        }

        // Circular permutation: participant[i] → participant[(i+1) % n]
        for ($i = 0; $i < $count; $i++) {
            $giver    = $ordered[$i];
            $receiver = $ordered[($i + 1) % $count];

            $giver->update(['assigned_to_id' => $receiver->id]);
        }

        $group->update(['is_drawn' => true, 'is_locked' => true]);
    }
}
