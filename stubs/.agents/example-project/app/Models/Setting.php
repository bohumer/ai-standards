<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'session_id',
        'lang',
        'flag',
        'key',
        'value'
    ];

    /**
     * Get a key-value list of settings for a specific flag (e.g. 'Product.category')
     */
    public static function getList(string $flag, string $key = null): array
    {
        $query = self::where('flag', $flag);
        
        if ($key) {
            $query->where('key', $key);
        }

        return $query->pluck('value', 'value')->toArray();
    }
}
