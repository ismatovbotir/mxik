<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GroupName; // Assuming you have a GroupName model
use App\Models\Product;

class Group extends Model
{
    protected $fillable = [];

    public function names()
    {
        return $this->hasMany(GroupName::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'group_id', 'id');
    }
}
