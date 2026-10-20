<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingRequirement extends Model
{
    use HasUuids;

    protected $fillable = [
        'onboarding_template_version_id',
        'name',
        'description',
        'is_required',
        'max_submissions',
        'sort_order',
        'settings',
    ];

    public function onboardingTemplateVersion(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplateVersion::class);
    }
}
