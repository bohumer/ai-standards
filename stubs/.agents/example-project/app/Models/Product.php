<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'is_active',
        'category',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Fields that can be searched using WithUniversalTable trait
     */
    public $searchable = [
        'name',
        'description',
    ];

    /**
     * Fields that can be sorted using WithUniversalTable trait
     */
    public $sortable = [
        'id',
        'name',
        'price',
        'created_at',
    ];

    /**
     * Fields that can be filtered using WithUniversalTable trait
     */
    public $filterable = [
        'is_active',
        'category',
    ];

    /**
     * Get options for filterable fields. Used by WithUniversalTable.
     */
    public static function getFilterOptions(string $field): array
    {
        return match ($field) {
            'is_active' => [
                1 => 'Aktívne',
                0 => 'Neaktívne',
            ],
            'category' => Setting::getList('product', 'category'),
            default => [],
        };
    }

    /**
     * The user who created the product.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
