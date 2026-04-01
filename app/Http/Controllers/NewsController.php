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
                $archiveItem = ArchiveNews::query()
                    ->where($slugColumn, $slug)
                    ->whereIn($statusColumn, ['publish', 'published'])
                    ->select([
                        $slugColumn.' as slug',
                        'guid',
                    ])
                    ->first();

                if ($archiveItem) {
                    $archiveBaseUrl = rtrim((string) config('archive.site_url', 'https://arsiv.rehagazetesi.com'), '/');
                    $newsPathPrefix = trim((string) config('archive.news_path_prefix', 'kose-yazilari'), '/');
                    $archiveUrl = null;

                    if (!empty($archiveItem->guid) && filter_var($archiveItem->guid, FILTER_VALIDATE_URL)) {
                        $archiveUrl = (string) $archiveItem->guid;
                    } else {
                        $archiveUrl = $archiveBaseUrl.'/'.($newsPathPrefix !== '' ? $newsPathPrefix.'/' : '').ltrim($slug, '/').'/';
                    }

                    $parsedBase = parse_url($archiveBaseUrl);
                    $parsedTarget = parse_url($archiveUrl);
                    if (is_array($parsedBase) && is_array($parsedTarget) && isset($parsedBase['scheme'], $parsedBase['host'])) {
                        $targetPath = $parsedTarget['path'] ?? '';
                        $targetQuery = isset($parsedTarget['query']) ? '?'.$parsedTarget['query'] : '';
                        $targetFragment = isset($parsedTarget['fragment']) ? '#'.$parsedTarget['fragment'] : '';
                        $archiveUrl = $parsedBase['scheme'].'://'.$parsedBase['host'].$targetPath.$targetQuery.$targetFragment;
                    }

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
