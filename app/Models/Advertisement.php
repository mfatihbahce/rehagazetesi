<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    public const PLACEMENT_LEFT = 'left_sidebar';
    public const PLACEMENT_RIGHT = 'right_sidebar';

    protected $fillable = [
        'title',
        'placement',
        'type',
        'image_url',
        'mobile_image_url',
        'target_url',
        'alt_text',
        'html_code',
        'is_active',
        'priority',
        'starts_at',
        'ends_at',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public static function placements(): array
    {
        return [
            self::PLACEMENT_LEFT => 'Sol Reklam Alanı',
            self::PLACEMENT_RIGHT => 'Sağ Reklam Alanı',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('is_active', true)
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }
}
