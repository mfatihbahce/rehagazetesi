<?php

namespace App\Repositories\Contracts;

use App\Models\News;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface NewsRepositoryInterface
{
    public function getPublished(int $limit = 10): Collection;

    public function getFeatured(int $limit = 5): Collection;

    public function getBreaking(int $limit = 5): Collection;

    public function getByCategory(int $categoryId, int $limit = 10): Collection;

    public function getByAuthor(int $userId, int $limit = 10): Collection;

    public function getPopular(int $limit = 10): Collection;

    public function getRelated(int $newsId, int $categoryId, int $limit = 4): Collection;

    public function findBySlug(string $slug): ?News;

    public function paginatePublished(int $perPage = 12): LengthAwarePaginator;

    public function paginateByCategory(int $categoryId, int $perPage = 12): LengthAwarePaginator;

    public function paginateByAuthor(int $userId, int $perPage = 12): LengthAwarePaginator;

    public function create(array $data): News;

    public function update(News $news, array $data): News;

    public function delete(News $news): bool;
}
