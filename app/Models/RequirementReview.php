<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequirementReview extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function requirementSubmission(): BelongsTo
    {
        return $this->belongsTo(RequirementSubmission::class);
    }
}
