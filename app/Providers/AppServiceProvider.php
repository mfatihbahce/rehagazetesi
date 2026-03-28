<?php

namespace App\Providers;

use App\Helpers\CacheKeys;
use App\Repositories\CategoryRepository;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\NewsRepositoryInterface;
use App\Repositories\NewsRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NewsRepositoryInterface::class, NewsRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.frontend', function ($view) {
            $breaking = Cache::remember(CacheKeys::BREAKING_NEWS, CacheKeys::TTL_BREAKING, function () {
                $items = \App\Models\News::where('status', 'published')
                    ->where('is_breaking', true)
                    ->whereNotNull('published_at')
                    ->orderBy('published_at', 'desc')
                    ->limit(5)
                    ->get();
                if ($items->isEmpty()) {
                    $items = \App\Models\News::where('status', 'published')
                        ->whereNotNull('published_at')
                        ->orderBy('published_at', 'desc')
                        ->limit(5)
                        ->get();
                }
                return $items;
            });
            $view->with('breakingNews', $breaking);
        });

        View::composer('layouts.admin', function ($view) {
            $count = Cache::remember(CacheKeys::PENDING_COUNT, CacheKeys::TTL_PENDING, function () {
                return \App\Models\News::where('status', 'pending')->count();
            });
            $view->with('pendingNewsCount', $count);
        });
    }
}
