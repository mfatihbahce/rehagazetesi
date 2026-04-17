<?php

namespace App\Providers;

use App\Helpers\CacheKeys;
use App\Models\Advertisement;
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

            $leftSidebarAds = Advertisement::query()
                ->active()
                ->where('placement', Advertisement::PLACEMENT_LEFT)
                ->orderByDesc('priority')
                ->orderByDesc('id')
                ->limit(3)
                ->get();

            $rightSidebarAds = Advertisement::query()
                ->active()
                ->where('placement', Advertisement::PLACEMENT_RIGHT)
                ->orderByDesc('priority')
                ->orderByDesc('id')
                ->limit(3)
                ->get();

            $renderedAdIds = $leftSidebarAds->pluck('id')
                ->merge($rightSidebarAds->pluck('id'))
                ->unique()
                ->values();

            if ($renderedAdIds->isNotEmpty()) {
                Advertisement::whereIn('id', $renderedAdIds)->increment('impressions');
            }

            $view->with('breakingNews', $breaking);
            $view->with('leftSidebarAds', $leftSidebarAds);
            $view->with('rightSidebarAds', $rightSidebarAds);
        });

        View::composer('frontend.home', function ($view) {
            $mobileHomeLeftAds = Advertisement::query()
                ->active()
                ->where('placement', Advertisement::PLACEMENT_LEFT)
                ->orderByDesc('priority')
                ->orderByDesc('id')
                ->limit(3)
                ->get();

            $mobileHomeRightAds = Advertisement::query()
                ->active()
                ->where('placement', Advertisement::PLACEMENT_RIGHT)
                ->orderByDesc('priority')
                ->orderByDesc('id')
                ->limit(3)
                ->get();

            $mobileHomeAdIds = $mobileHomeLeftAds->pluck('id')
                ->merge($mobileHomeRightAds->pluck('id'))
                ->unique()
                ->values();

            if ($mobileHomeAdIds->isNotEmpty()) {
                Advertisement::whereIn('id', $mobileHomeAdIds)->increment('impressions');
            }

            $view->with('mobileHomeLeftAds', $mobileHomeLeftAds);
            $view->with('mobileHomeRightAds', $mobileHomeRightAds);
        });

        View::composer('layouts.admin', function ($view) {
            $count = Cache::remember(CacheKeys::PENDING_COUNT, CacheKeys::TTL_PENDING, function () {
                return \App\Models\News::where('status', 'pending')->count();
            });
            $view->with('pendingNewsCount', $count);
        });
    }
}
