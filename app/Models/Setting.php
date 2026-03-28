<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public $timestamps = true;

    private const CACHE_KEY = 'site_settings';
    private const CACHE_TTL = 3600; // 1 saat

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $all = self::allCached();

        return $all[$key] ?? $default;
    }

    public static function getAll(): array
    {
        return self::allCached();
    }

    public static function set(string $key, mixed $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
        );
        self::clearCache();
    }

    public static function setMany(array $items): void
    {
        foreach ($items as $key => $value) {
            self::set($key, $value);
        }
    }

    private static function allCached(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $rows = self::all();
            $result = [];
            foreach ($rows as $row) {
                $val = $row->value;
                if ($val === '1' || $val === '0') {
                    $result[$row->key] = $val === '1';
                } elseif (is_numeric($val)) {
                    $result[$row->key] = (int) $val;
                } else {
                    $result[$row->key] = $val;
                }
            }
            return $result;
        });
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
