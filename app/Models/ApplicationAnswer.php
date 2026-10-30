<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationAnswer extends Model
{
    use HasUuids;

    protected $fillable = [
        'application_id',
        'form_field_id',
        'value',
        'file_path',
        'file_name',
        'file_mime',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function formField(): BelongsTo
    {
        return $this->belongsTo(FormField::class);
    }
}
