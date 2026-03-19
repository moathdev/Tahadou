<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'name',
        'phone_number',
        'gender',
        'interests',
        'edit_token',
        'assigned_to_id',
    ];

    protected $casts = [
        'interests' => 'array',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'assigned_to_id');
    }

    public function assignedFrom(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'id', 'assigned_to_id');
    }

    /**
     * Format phone number for WhatsApp (ensure international format).
     */
    public function getWhatsappNumberAttribute(): string
    {
        $phone = preg_replace('/\D/', '', $this->phone_number);

        if (str_starts_with($phone, '0')) {
            $phone = '966' . ltrim($phone, '0');
        }

        return $phone;
    }
}
