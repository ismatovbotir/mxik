<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Group; // Assuming you have a Group model

class GroupName extends Model
{
    protected $fillable = [];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
