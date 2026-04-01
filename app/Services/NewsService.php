<?php

namespace App\Services;

use App\Helpers\CacheKeys;
use App\Models\ArchiveNews;
use App\Models\News;
use App\Models\User;
use App\Repositories\Contracts\NewsRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsService
{
    public function __construct(
        protected NewsRepositoryInterface $newsRepository,
        protected MediaService $mediaService
    ) {}

    public function getHomePageData(): array
    {
        return Cache::remember(CacheKeys::HOME_PAGE, CacheKeys::TTL_HOME, function () {
            $categories = \App\Models\Category::where('is_active', true)->orderBy('order')->get();
            $categoryNews = [];
            foreach ($categories as $cat) {
                $categoryNews[$cat->id] = $this->newsRepository->getByCategory($cat->id, 4);
            }

            return [
                'breaking' => $this->newsRepository->getBreaking(5),
                'featured' => $this->newsRepository->getFeatured(5),
                'latest' => $this->newsRepository->getPublished(12),
                'popular' => $this->newsRepository->getPopular(10),
                'editors' => User::where('role', 'editor')
                    ->with('editorProfile')
                    ->where('can_access_archive', true)
                    ->whereNotNull('legacy_user_id')
                    ->orderByDesc('is_chief_columnist')
                    ->orderByRaw('CASE WHEN editor_order IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('editor_order')
                    ->orderBy('name')
                    ->limit(4)
                    ->get(),
                'categories' => $categories,
                'categoryNews' => $categoryNews,
            ];
        });
    }

    public function getNewsByCategory(int $categoryId): array
    {
        return [
            'category' => \App\Models\Category::findOrFail($categoryId),
            'news' => $this->newsRepository->paginateByCategory($categoryId),
        ];
    }

    public function getNewsByAuthor(int $userId): array
    {
        $author = User::with('editorProfile')->findOrFail($userId);

        if (
            config('archive.enabled') &&
            $author->can_access_archive &&
            $author->legacy_user_id
        ) {
            $authorKey = config('archive.columns.news.author_key', 'user_id');
            $titleColumn = config('archive.columns.news.title', 'title');
            $slugColumn = config('archive.columns.news.slug', 'slug');
            $statusColumn = config('archive.columns.news.status', 'status');
            $publishedAtColumn = config('archive.columns.news.published_at', 'created_at');
            $primaryKey = config('archive.columns.news.primary_key', 'id');
            $excerptColumn = config('archive.columns.news.excerpt', 'post_excerpt');
            $featuredImageMetaKey = config('archive.columns.news.featured_image_meta_key', '_thumbnail_id');
            $newsTable = str_replace('`', '', config('archive.tables.news', 'wp_posts'));
            $postmetaTable = str_replace('`', '', config('archive.tables.postmeta', 'wp_postmeta'));
            $newsTableRef = "`{$newsTable}`";
            $postmetaTableRef = "`{$postmetaTable}`";

            $news = ArchiveNews::query()
                ->where($authorKey, $author->legacy_user_id)
                ->whereIn($statusColumn, ['publish', 'published'])
                ->select([
                    DB::raw("{$primaryKey} as id"),
                    DB::raw("{$titleColumn} as title"),
                    DB::raw("{$slugColumn} as slug"),
                    DB::raw("{$statusColumn} as status"),
                    DB::raw("{$publishedAtColumn} as published_at"),
                    DB::raw("{$excerptColumn} as excerpt"),
                    DB::raw("post_type as post_type"),
                    DB::raw("guid as guid"),
                    DB::raw("(SELECT att.guid FROM {$postmetaTableRef} pm INNER JOIN {$newsTableRef} att ON att.`{$primaryKey}` = pm.meta_value WHERE pm.post_id = {$newsTableRef}.`{$primaryKey}` AND pm.meta_key = '{$featuredImageMetaKey}' LIMIT 1) as featured_image"),
                ])
                ->orderByDesc($publishedAtColumn)
                ->paginate(12);

            return [
                'author' => $author,
                'news' => $news,
                'usesArchiveNews' => true,
            ];
        }

        return [
            'author' => $author,
            'news' => $this->newsRepository->paginateByAuthor($userId),
            'usesArchiveNews' => false,
        ];
    }

    public function getNewsDetail(string $slug): ?News
    {
        $news = $this->newsRepository->findBySlug($slug);
        if ($news) {
            $news->incrementViews();
            $news->load(['category', 'author.editorProfile']);
        }
        return $news;
    }

    public function getRelatedNews(News $news): Collection
    {
        return $this->newsRepository->getRelated($news->id, $news->category_id);
    }

    public function search(string $query): LengthAwarePaginator
    {
        $query = trim($query);
        if ($query === '') {
            return News::whereRaw('1 = 0')->paginate(12);
        }

        $baseQuery = News::where('status', 'published')
            ->whereNotNull('published_at')
            ->with(['category', 'author'])
            ->orderBy('published_at', 'desc');

        if (\DB::getDriverName() === 'mysql' && strlen($query) >= 2) {
            try {
                return $baseQuery
                    ->whereRaw('MATCH(title, excerpt, content) AGAINST(? IN NATURAL LANGUAGE MODE)', [$query])
                    ->paginate(12);
            } catch (\Throwable $e) {
                // FULLTEXT index yoksa veya hata olursa LIKE fallback
            }
        }

        $escaped = str_replace(['%', '_', '\\'], ['\\%', '\\_', '\\\\'], $query);
        return $baseQuery
            ->where(function ($q) use ($escaped) {
                $q->where('title', 'like', "%{$escaped}%")
                    ->orWhere('content', 'like', "%{$escaped}%")
                    ->orWhere('excerpt', 'like', "%{$escaped}%");
            })
            ->paginate(12);
    }

    public function createNews(User $user, array $data): News
    {
        $data['user_id'] = $user->id;
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['status'] = $user->isAdmin() ? ($data['status'] ?? 'published') : 'pending';
        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }
        $data['is_breaking'] = $user->isAdmin() ? ($data['is_breaking'] ?? false) : false;
        $data['is_featured'] = $user->isAdmin() ? ($data['is_featured'] ?? false) : false;

        if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
            $data['featured_image'] = $this->mediaService->uploadNewsImage($data['featured_image']);
        } elseif (!empty($data['featured_image_path'])) {
            $data['featured_image'] = $data['featured_image_path'];
        }
        unset($data['featured_image_path']);

        $news = $this->newsRepository->create($data);
        CacheKeys::clearNewsCaches();
        return $news;
    }

    public function updateNews(News $news, User $user, array $data): News
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        if (!$user->isAdmin()) {
            unset($data['is_breaking'], $data['is_featured'], $data['status']);
        }
        if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
            $data['featured_image'] = $this->mediaService->uploadNewsImage($data['featured_image']);
        } elseif (!empty($data['featured_image_path'])) {
            $data['featured_image'] = $data['featured_image_path'];
        } else {
            unset($data['featured_image']);
        }
        unset($data['featured_image_path']);

        $updated = $this->newsRepository->update($news, $data);
        CacheKeys::clearNewsCaches();
        return $updated;
    }

    public function approveNews(News $news): News
    {
        $updated = $this->newsRepository->update($news, [
            'status' => 'published',
            'published_at' => now(),
        ]);
        CacheKeys::clearNewsCaches();
        return $updated;
    }

    public function rejectNews(News $news): News
    {
        $updated = $this->newsRepository->update($news, ['status' => 'rejected']);
        CacheKeys::clearNewsCaches();
        return $updated;
    }

    public function deleteNews(News $news): bool
    {
        $deleted = $this->newsRepository->delete($news);
        if ($deleted) {
            CacheKeys::clearNewsCaches();
        }
        return $deleted;
    }
}
