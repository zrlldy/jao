<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormFieldOption extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function formField(): BelongsTo
    {
        return $this->belongsTo(FormField::class);
    }
}
