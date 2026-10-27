<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Translation extends Model
{
    protected $table = 'translations';

    public $timestamps = false;

    protected $fillable = [
        'lang',
        'key',
        'value',
    ];

    protected static array $runtimeCache = [];

    protected static function booted(): void
    {
        $flush = static function (self $model): void {
            Cache::forget("all_translations_{$model->lang}");
            unset(self::$runtimeCache[$model->lang]);
        };

        static::saved($flush);
        static::deleted($flush);
    }

    /**
     * Resolve static UI translation for the active locale without fallback strings.
     */
    public static function translate(string $key, array $replace = []): string
    {
        if ($key === '') {
            return '';
        }

        $lang = app_locale();

        if (! isset(self::$runtimeCache[$lang])) {
            self::$runtimeCache[$lang] = Cache::remember("all_translations_{$lang}", 3600, static function () use ($lang): array {
                $dictionary = [];
                foreach (self::query()->where('lang', $lang)->get(['key', 'value']) as $row) {
                    $dictionary[mb_strtolower($row->key, 'UTF-8')] = $row->value;
                }

                return $dictionary;
            });
        }

        $normalizedKey = mb_strtolower($key, 'UTF-8');
        $value = self::$runtimeCache[$lang][$normalizedKey] ?? null;

        if ($value === null || $value === '') {
            return '#Trans.' . $key;
        }

        foreach ($replace as $placeholder => $replacement) {
            $value = str_replace(':' . $placeholder, (string) $replacement, $value);
        }

        return $value;
    }
}
