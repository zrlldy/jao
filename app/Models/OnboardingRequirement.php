<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingRequirement extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function onboardingTemplateVersion(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplateVersion::class);
    }
}
