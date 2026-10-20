<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Applicant extends Model
{
    use HasApiTokens, HasUuids;

    public $incrementing = false;

    protected $fillable = [
        'google_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'gender',
        'address',
        'email',
        'phone_number',
        'avatar_url',
        'birth_date',
        'email_verified_at',
    ];

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
