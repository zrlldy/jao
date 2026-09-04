<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationInvitation extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function jobHiring(): BelongsTo
    {
        return $this->belongsTo(JobHiring::class);
    }
}
