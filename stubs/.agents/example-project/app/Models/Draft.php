<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Draft extends Model
{
    protected $table = 'drafts';

    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'context_key',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    // Relationship -- Belongs to User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
