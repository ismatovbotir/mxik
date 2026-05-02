<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageCode extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'class_code_id',
        'name',
        'package_type',
    ];

    public function classCode(): BelongsTo
    {
        return $this->belongsTo(ClassCode::class, 'class_code_id');
    }
}
