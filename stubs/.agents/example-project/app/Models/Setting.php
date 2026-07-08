<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
     * Relationship -- One To One (Polymorphic) - Content text
     */
    public function content(): MorphOne
    {
        return $this->morphOne(Text::class, 'model')->where('flag', 'content');
    }

    /**
     * Relationship -- MorphMany - Files
     */
    public function files(): MorphMany
    {
        // Assuming we will have a File model later like in popular
        return $this->morphMany(File::class, 'model');
    }

    /**
     * Get a key-value list of settings for a specific flag (e.g. 'Article.type')
     */
    public static function getList(string $flag, string $lang = 'sk'): array
    {
        return self::where('flag', $flag)
            ->where(function ($q) use ($lang) {
                $q->where('lang', $lang)->orWhereNull('lang')->orWhere('lang', '');
            })
            ->pluck('value', 'key')
            ->toArray();
    }
}
