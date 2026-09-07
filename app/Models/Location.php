<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasUuids, HasFactory;

    protected $guarded = [];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function jobHirings(): HasMany
    {
        return $this->hasMany(JobHiring::class);
    }
}
