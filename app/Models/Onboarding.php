<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Onboarding extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function onboardingTemplate(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class);
    }

    public function onboardingRequireInstances(): HasMany
    {
        return $this->hasMany(OnboardingRequireInstance::class);
    }
}
