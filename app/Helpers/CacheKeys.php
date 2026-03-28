<?php

namespace App\Helpers;

class CacheKeys
{
    public const HOME_PAGE = 'news.home_page';
    public const BREAKING_NEWS = 'news.breaking';
    public const PENDING_COUNT = 'news.pending_count';

    public const TTL_HOME = 300;      // 5 dakika
    public const TTL_BREAKING = 120;  // 2 dakika
    public const TTL_PENDING = 60;    // 1 dakika

    public static function clearNewsCaches(): void
    {
        \Illuminate\Support\Facades\Cache::forget(self::HOME_PAGE);
        \Illuminate\Support\Facades\Cache::forget(self::BREAKING_NEWS);
        \Illuminate\Support\Facades\Cache::forget(self::PENDING_COUNT);
    }
}
