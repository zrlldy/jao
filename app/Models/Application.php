<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Application extends Model
{
    use HasUuids;

    protected $fillable = [
        'applicant_no',
        'application_invitation_id',
        'job_hiring_id',
        'form_version_id',
        'status',
        'submitted_at',
        'reviewed_by',
        'rejection_reason',
        'reviewed_at',
        'deleted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'reviewed_by' => 'string',
    ];

    protected static function booted(): void
    {
        static::creating(function ($application) {
            do {
                $applicationNo =
                    '3E-APP-' .
                    now()->format('Y') .
                    '-' .
                    Str::upper(Str::random(6));
            } while (
                static::where('application_no', $applicationNo)->exists()
            );

            $application->application_no = $applicationNo;
        });
    }

    public function applicationInvitation(): BelongsTo
    {
        return $this->belongsTo(ApplicationInvitation::class);
    }

    public function jobHiring(): BelongsTo
    {
        return $this->belongsTo(JobHiring::class);
    }

    public function formVersion(): BelongsTo
    {
        return $this->belongsTo(FormVersion::class);
    }

    public function applicationAnswers(): HasMany
    {
        return $this->hasMany(ApplicationAnswer::class);
    }

    public function applicationStatusHistories(): HasMany
    {
        return $this->hasMany(ApplicationStatusHistory::class);
    }

    public function onboardings(): HasMany
    {
        return $this->hasMany(Onboarding::class);
    }
}
