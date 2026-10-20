<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormSection extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'form_version_id',
        'title',
        'description',
        'sort_order',
    ];

    public function formVersion(): BelongsTo
    {
        return $this->belongsTo(FormVersion::class);
    }

    public function formFields(): HasMany
    {
        return $this->hasMany(FormField::class);
    }
}
