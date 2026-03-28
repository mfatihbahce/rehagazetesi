<?php

namespace App\Http\Controllers;

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
