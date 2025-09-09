<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Unit extends Model
{
    public $timestamps = false;
    public $guarded = [];

    public function product()
    {
        return $this->belongsTo( Product::class);
    }
}
