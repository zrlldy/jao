<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class ApplicationInvitation extends Model
{
    use HasFactory, HasUuids, Notifiable;

    protected $fillable = [
        'job_hiring_id',
        'email',
        'token',
        'expired_at',
        'used_at',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function jobHiring(): BelongsTo
    {
        return $this->belongsTo(JobHiring::class);
    }
}
