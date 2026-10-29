<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JobHiring extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'department',
        'location',
        'employment_type',
        'title',
        'description',
        'requirements',
        'status',
        'published_at',
        'closed_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (JobHiring $jobHiring): void {
            $jobHiring->slug = (Str::slug($jobHiring->title) ?: 'job').'-'.$jobHiring->getKey();
        });
    }

    #[Scope]
    protected function publiclyVisible(Builder $query): void
    {
        $query->where('status', 'published')
            ->where(fn (Builder $query) => $query->whereNull('published_at')->orWhereDate('published_at', '<=', today()))
            ->where(fn (Builder $query) => $query->whereNull('closed_at')->orWhereDate('closed_at', '>=', today()));
    }

    public function formVersions(): BelongsToMany
    {
        return $this->belongsToMany(FormVersion::class, 'job_form_version_assignments')->withTimestamps();
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
