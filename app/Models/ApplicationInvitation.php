<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class ApplicationInvitation extends Model
{
    use HasUuids,Notifiable;

    protected $guarded = [];

    public function jobHiring(): BelongsTo
    {
        return $this->belongsTo(JobHiring::class);
    }
}
