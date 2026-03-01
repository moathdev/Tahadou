<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'max_participants',
        'max_gift_price',
        'admin_code',
        'admin_lookup',
        'is_locked',
        'is_drawn',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_drawn'  => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Group $group) {
            if (empty($group->uuid)) {
                $group->uuid = Str::uuid();
            }
        });
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function isFull(): bool
    {
        return $this->participants()->count() >= $this->max_participants;
    }

    public function canDraw(): bool
    {
        return $this->participants()->count() >= 3 && ! $this->is_drawn && ! $this->is_locked === false;
    }

    public function getShareableLinkAttribute(): string
    {
        return route('participant.register', ['uuid' => $this->uuid]);
    }
}
