<?php

namespace App\Services;

use App\Helpers\CacheKeys;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getAllActive()
    {
        return $this->categoryRepository->getAllActive();
    }

    public function getAll()
    {
        return $this->categoryRepository->getAll();
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->categoryRepository->findBySlug($slug);
    }

    public function create(array $data): Category
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $category = $this->categoryRepository->create($data);
        CacheKeys::clearNewsCaches();
        return $category;
    }

    public function update(Category $category, array $data): Category
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $updated = $this->categoryRepository->update($category, $data);
        CacheKeys::clearNewsCaches();
        return $updated;
    }

    public function delete(Category $category): bool
    {
        $deleted = $this->categoryRepository->delete($category);
        if ($deleted) {
            CacheKeys::clearNewsCaches();
        }
        return $deleted;
    }
}
