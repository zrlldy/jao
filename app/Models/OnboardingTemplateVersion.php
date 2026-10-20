<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingTemplateVersion extends Model
{
    use HasUuids;

    protected $fillable = [
        'onboarding_template_id',
        'version',
        'status',
        'published_at',
    ];

    public function onboardingTemplate(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class);
    }

    public function onboardingRequirements(): HasMany
    {
        return $this->hasMany(OnboardingRequirement::class);
    }
}
