<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormTemplate extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function formVersions(): HasMany
    {
        return $this->hasMany(FormVersion::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
