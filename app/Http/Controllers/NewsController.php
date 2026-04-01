<?php

namespace App\Http\Controllers;

use App\Models\ArchiveNews;
use App\Services\NewsService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function __construct(
        protected NewsService $newsService
    ) {}

    /**
     * Haber detay sayfası
     */
    public function show(string $slug)
    {
        $news = $this->newsService->getNewsDetail($slug);

        if (!$news) {
            if (config('archive.enabled')) {
                $slugColumn = config('archive.columns.news.slug', 'slug');
                $statusColumn = config('archive.columns.news.status', 'status');
                $existsInArchive = ArchiveNews::query()
                    ->where($slugColumn, $slug)
                    ->whereIn($statusColumn, ['publish', 'published'])
                    ->exists();

                if ($existsInArchive) {
                    $archiveBaseUrl = rtrim(config('archive.site_url', 'https://arsiv.rehagazetesi.com'), '/');
                    $newsPathPrefix = trim((string) config('archive.news_path_prefix', 'kose-yazilari'), '/');
                    $archiveUrl = $archiveBaseUrl.'/'.($newsPathPrefix !== '' ? $newsPathPrefix.'/' : '').ltrim($slug, '/').'/';

                    return redirect()->away($archiveUrl, 301);
                }
            }

            abort(404);
        }

        $relatedNews = $this->newsService->getRelatedNews($news);

        return view('frontend.news.show', compact('news', 'relatedNews'));
    }

    /**
     * Arama
     */
    public function search(Request $request)
    {
        $query = (string) ($request->input('q') ?? '');
        $news = $this->newsService->search($query);

        return view('frontend.news.search', compact('news', 'query'));
    }
}
