<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Applicant extends Model
{
    use HasUuids, HasApiTokens;

    public $incrementing = false;
    protected $guarded = [];
    protected $keyType = 'string';
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
