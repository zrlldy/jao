<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormField extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'form_section_id',
        'key',
        'label',
        'type',
        'placeholder',
        'default_value',
        'is_required',
        'sort_order',
        'settings',
        'validation_rules',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'validation_rules' => 'array',
        'settings' => 'array',
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
