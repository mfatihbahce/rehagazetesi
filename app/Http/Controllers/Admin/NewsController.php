<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Models\News;
use App\Repositories\Contracts\NewsRepositoryInterface;
use App\Services\NewsService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function __construct(
        protected NewsService $newsService,
        protected NewsRepositoryInterface $newsRepository
    ) {}

    /**
     * Haber listesi
     */
    public function index(Request $request)
    {
        $query = News::with(['category', 'author']);

        if ($request->user()->isEditor()) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $news = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.news.index', compact('news'));
    }

    /**
     * Yeni haber formu
     */
    public function create()
    {
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.news.create', compact('categories'));
    }

    /**
     * Haber kaydet
     */
    public function store(StoreNewsRequest $request)
    {
        $news = $this->newsService->createNews($request->user(), $request->validated());

        return redirect()->route('admin.news.index')
            ->with('success', 'Haber başarıyla oluşturuldu.');
    }

    /**
     * Haber düzenleme formu
     */
    public function edit(Request $request, News $news)
    {
        $this->authorizeNews($news);
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.news.edit', compact('news', 'categories'));
    }

    /**
     * Haber güncelle
     */
    public function update(UpdateNewsRequest $request, News $news)
    {
        $this->authorizeNews($news);
        $this->newsService->updateNews($news, $request->user(), $request->validated());

        return redirect()->route('admin.news.index')
            ->with('success', 'Haber başarıyla güncellendi.');
    }

    /**
     * Haber sil (sadece admin)
     */
    public function destroy(Request $request, News $news)
    {
        if (!$request->user()->isAdmin()) {
            abort(403);
        }
        $this->newsService->deleteNews($news);

        return redirect()->route('admin.news.index')
            ->with('success', 'Haber silindi.');
    }

    /**
     * Haber onayla
     */
    public function approve(Request $request, News $news)
    {
        if (!$request->user()->isAdmin()) {
            abort(403);
        }
        $this->newsService->approveNews($news);

        return back()->with('success', 'Haber yayına alındı.');
    }

    /**
     * Haber reddet
     */
    public function reject(Request $request, News $news)
    {
        if (!$request->user()->isAdmin()) {
            abort(403);
        }
        $this->newsService->rejectNews($news);

        return back()->with('success', 'Haber reddedildi.');
    }

    private function authorizeNews(News $news): void
    {
        $user = request()->user();
        if (!$user || ($user->isEditor() && $news->user_id !== $user->id)) {
            abort(403);
        }
    }
}
