<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingTemplate extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function onboardings(): HasMany
    {
        return $this->hasMany(Onboarding::class);
    }

    public function onboardingTemplateVersions(): HasMany
    {
        return $this->hasMany(OnboardingTemplateVersion::class);
    }

    public function jobHirings(): BelongsToMany
    {
        return $this->belongsToMany(JobHiring::class, 'job_onboarding_template_version');
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
