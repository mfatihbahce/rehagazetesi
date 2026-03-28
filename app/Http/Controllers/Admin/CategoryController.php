<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    /**
     * Kategori listesi
     */
    public function index()
    {
        $categories = $this->categoryService->getAll();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Yeni kategori formu
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Kategori kaydet
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $this->categoryService->create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori başarıyla oluşturuldu.');
    }

    /**
     * Kategori düzenleme formu
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Kategori güncelle
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $this->categoryService->update($category, $data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori güncellendi.');
    }

    /**
     * Kategori sil
     */
    public function destroy(Category $category)
    {
        if ($category->news()->count() > 0) {
            return back()->with('error', 'Bu kategoride haberler var, önce haberleri taşıyın veya silin.');
        }

        $this->categoryService->delete($category);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori silindi.');
    }
}
