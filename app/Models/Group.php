<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GroupName; // Assuming you have a GroupName model

class Group extends Model
{
    protected $fillable = [];

    public function names()
    {
        return $this->hasMany(GroupName::class);
    }
}
