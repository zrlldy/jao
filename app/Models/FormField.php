<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormField extends Model
{
    use HasUuids;

    protected $guarded = [];
    protected $casts = [
        'is_required' => 'boolean',
        'validation_rules' => 'json',
        'settings' => 'json'
    ];

    public function formFieldOptions(): HasMany
    {
        return $this->hasMany(FormFieldOption::class);
    }

    public function formSection(): BelongsTo
    {
        return $this->belongsTo(FormSection::class);
    }
}
