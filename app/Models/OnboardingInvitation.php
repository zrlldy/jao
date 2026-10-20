<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingInvitation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'onboarding_id',
        'email',
        'token',
        'expired_at',
        'used_at',
    ];

    protected $casts = ['expired_at' => 'date', 'used_at' => 'date'];

    public function onboarding(): BelongsTo
    {
        return $this->belongsTo(Onboarding::class);
    }
}
