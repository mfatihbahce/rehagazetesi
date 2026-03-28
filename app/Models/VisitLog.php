<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    protected $fillable = ['path', 'ip', 'user_agent', 'referrer', 'visited_at'];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public $timestamps = false;

    public static function log(string $path, string $ip, ?string $userAgent, ?string $referrer): void
    {
        static::create([
            'path' => $path,
            'ip' => $ip,
            'user_agent' => $userAgent ? mb_substr($userAgent, 0, 512) : null,
            'referrer' => $referrer ? mb_substr($referrer, 0, 500) : null,
            'visited_at' => now(),
        ]);
    }
}
