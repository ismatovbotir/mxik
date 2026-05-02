<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GtinPrefix extends Model
{
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $primaryKey = 'prefix';

    protected $fillable = ['prefix', 'country', 'country_code'];

    public static function forGtin(string $gtin): ?self
    {
        // Try longest prefix first (3 digits), then 2, then 1
        foreach ([3, 2, 1] as $len) {
            $match = static::find(substr($gtin, 0, $len));
            if ($match) {
                return $match;
            }
        }

        return null;
    }
}
