<?php

namespace App\Models;

use App\Traits\HasJsonTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasJsonTranslations;

    protected $table = 'settings';

    protected $fillable = [
        'flag',
        'key',
        'value',
        'label',
        'payload',
    ];

    public array $translatable = [
        'label',
    ];

    public array $searchable = [
        'value',
        'label',
    ];

    public array $sortable = [
        'id',
        'value',
        'created_at',
    ];

    public array $filterable = [];

    protected static array $runtimeCache = [];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    protected static function booted(): void
    {
        $flush = static function (self $model): void {
            $prefix = "setting_{$model->flag}_{$model->key}";
            Cache::forget($prefix);
            unset(self::$runtimeCache[$prefix]);
        };

        static::saved($flush);
        static::deleted($flush);
    }

    /**
     * Return associative array [value => translated label] for select inputs.
     */
    public static function getList(string $flag, string $key): array
    {
        $items = self::getCachedItems($flag, $key);
        $locale = app_locale();
        $fallback = config('app.fallback_locale', 'sk');

        $list = [];
        foreach ($items as $item) {
            $labels = $item['labels'];
            $list[$item['value']] = $labels[$locale]
                ?? $labels[$fallback]
                ?? reset($labels)
                ?: $item['value'];
        }

        return $list;
    }

    /**
     * Return raw option codes for a setting group (e.g. ['sk', 'en']).
     */
    public static function getCodes(string $flag, string $key): array
    {
        return array_column(self::getCachedItems($flag, $key), 'value');
    }

    protected static function getCachedItems(string $flag, string $key): array
    {
        $cacheKey = "setting_{$flag}_{$key}";

        if (! isset(self::$runtimeCache[$cacheKey])) {
            self::$runtimeCache[$cacheKey] = Cache::remember($cacheKey, 3600, static function () use ($flag, $key): array {
                return self::query()
                    ->where('flag', $flag)
                    ->where('key', $key)
                    ->orderBy('id')
                    ->get()
                    ->map(static fn (self $row) => [
                        'value' => $row->value,
                        'labels' => array_filter($row->getTranslations('label'), static fn ($v) => filled($v)),
                    ])
                    ->all();
            });
        }

        return self::$runtimeCache[$cacheKey];
    }
}
