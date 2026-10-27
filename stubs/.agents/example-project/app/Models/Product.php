<?php

namespace App\Models;

use App\Traits\HasJsonTranslations;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasJsonTranslations;

    protected $table = 'products';

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'category',
        'price',
        'is_active',
        'published_at',
    ];

    public array $translatable = [
        'name',
        'slug',
        'description',
    ];

    public array $searchable = [
        'name',
        'description',
    ];

    public array $sortable = [
        'id',
        'name',
        'price',
        'created_at',
    ];

    public array $filterable = [
        'is_active',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    // Relationship -- Belongs to creator User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessor -- formatted price with currency symbol
    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format((float) $this->price, 2, ',', ' ') . ' €',
        );
    }

    /**
     * Filter options used by WithUniversalTable.
     */
    public static function getFilterOptions(string $field): array
    {
        return match ($field) {
            'is_active' => [
                1 => t('admin.common.active'),
                0 => t('admin.common.inactive'),
            ],
            'category' => Setting::getList('product', 'category'),
            default => [],
        };
    }
}
