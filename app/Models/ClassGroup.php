<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassGroup extends Model
{
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['id', 'name_uz', 'name_ru', 'name_lat'];

    public function classCodes(): HasMany
    {
        return $this->hasMany(ClassCode::class, 'class_group_id');
    }
}
