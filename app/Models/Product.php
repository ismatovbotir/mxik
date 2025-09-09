<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Gtin;
use App\Models\Group;
use App\Models\Unit;


class Product extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function gtin()
    {
        return $this->belongsTo(Gtin::class);
    }
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
