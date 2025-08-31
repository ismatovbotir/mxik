<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gtin extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
}
