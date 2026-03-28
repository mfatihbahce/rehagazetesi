<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\NewsRepositoryInterface;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
        protected NewsRepositoryInterface $newsRepository
    ) {}

    /**
     * Slug ile kategori sayfası
     */
    public function show(string $slug)
    {
        $category = $this->categoryService->findBySlug($slug);

        if (!$category) {
            abort(404);
        }

        $news = $this->newsRepository->paginateByCategory($category->id);

        return view('frontend.news.category', compact('category', 'news'));
    }
}
