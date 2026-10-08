<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingRequireInstance extends Model
{
    use HasUuids;

    protected $fillable = [
        'onboarding_id',
        'status',
        'submission_count',
        'completed_at',
    ];

    public function onboarding(): BelongsTo
    {
        return $this->belongsTo(Onboarding::class);
    }

    public function requirementSubmissions(): HasMany
    {
        return $this->hasMany(RequirementSubmission::class);
    }
}
