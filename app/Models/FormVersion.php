<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormVersion extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function formTemplate(): BelongsTo
    {
        return $this->belongsTo(FormTemplate::class);
    }

    public function jobHirings(): BelongsToMany
    {
        return $this->belongsToMany(JobHiring::class, 'job_form_version_assignments');
    }

    public function formSections(): HasMany
    {
        return $this->hasMany(FormSection::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
