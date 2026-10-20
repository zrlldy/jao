<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormFieldOption extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'form_field_id',
        'value',
        'label',
        'sort_order',
    ];

    public function formField(): BelongsTo
    {
        return $this->belongsTo(FormField::class);
    }
}
