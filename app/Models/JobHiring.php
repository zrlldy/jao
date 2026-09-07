<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobHiring extends Model
{
    use HasUuids, HasFactory;

    protected $guarded = [];
    protected $casts = [
        // 'is_active' => 'boolean',
        'published_at' => 'datetime',
        // 'closed_at' => 'datetime',

    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function formVersions(): BelongsToMany
    {
        return $this->belongsToMany(FormVersion::class, 'job_form_version_assignments');
    }

    public function onboardingTemplates(): BelongsToMany
    {
        return $this->belongsToMany(OnboardingTemplate::class, 'job_onboarding_template_version');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function applicationInvitations(): HasMany
    {
        return $this->hasMany(ApplicationInvitation::class);
    }
}
