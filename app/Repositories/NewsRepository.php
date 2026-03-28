<?php

namespace App\Repositories;

use App\Models\News;
use App\Repositories\Contracts\NewsRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NewsRepository implements NewsRepositoryInterface
{
    public function __construct(
        protected News $model
    ) {}

    public function getPublished(int $limit = 10): Collection
    {
        return $this->model
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getFeatured(int $limit = 5): Collection
    {
        return $this->model
            ->where('status', 'published')
            ->where('is_featured', true)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getBreaking(int $limit = 5): Collection
    {
        return $this->model
            ->where('status', 'published')
            ->where('is_breaking', true)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getByCategory(int $categoryId, int $limit = 10): Collection
    {
        return $this->model
            ->where('status', 'published')
            ->where('category_id', $categoryId)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getByAuthor(int $userId, int $limit = 10): Collection
    {
        return $this->model
            ->where('status', 'published')
            ->where('user_id', $userId)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getPopular(int $limit = 10): Collection
    {
        return $this->model
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRelated(int $newsId, int $categoryId, int $limit = 4): Collection
    {
        return $this->model
            ->where('status', 'published')
            ->where('category_id', $categoryId)
            ->where('id', '!=', $newsId)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function findBySlug(string $slug): ?News
    {
        return $this->model
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();
    }

    public function findBySlugForAdmin(string $slug): ?News
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function paginatePublished(int $perPage = 12): LengthAwarePaginator
    {
        return $this->model
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->with(['category', 'author'])
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    public function paginateByCategory(int $categoryId, int $perPage = 12): LengthAwarePaginator
    {
        return $this->model
            ->where('status', 'published')
            ->where('category_id', $categoryId)
            ->whereNotNull('published_at')
            ->with(['category', 'author'])
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    public function paginateByAuthor(int $userId, int $perPage = 12): LengthAwarePaginator
    {
        return $this->model
            ->where('status', 'published')
            ->where('user_id', $userId)
            ->whereNotNull('published_at')
            ->with(['category', 'author'])
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): News
    {
        return $this->model->create($data);
    }

    public function update(News $news, array $data): News
    {
        $news->update($data);
        return $news->fresh();
    }

    public function delete(News $news): bool
    {
        return $news->delete();
    }
}
