<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassCode extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'new_mxik_code',
        'status',
        'class_group_id',
        'name',
        'description',
        'gtin',
        'label',
        'use_package',
        'only_card',
    ];

    protected $casts = [
        'label' => 'boolean',
        'use_package' => 'boolean',
        'only_card' => 'boolean',
    ];

    public function packageCodes(): HasMany
    {
        return $this->hasMany(PackageCode::class, 'class_code_id');
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class);
    }
}
