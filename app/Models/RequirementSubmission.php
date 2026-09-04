<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequirementSubmission extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function onboardingRequireInstance(): BelongsTo
    {
        return $this->belongsTo(OnboardingRequireInstance::class);
    }

    public function requirementReviews(): HasMany
    {
        return $this->hasMany(RequirementReview::class);
    }

    public function submissionFiles(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }
}
