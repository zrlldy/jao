<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmploymentType extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function jobHirings(): HasMany
    {
        return $this->hasMany(JobHiring::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
