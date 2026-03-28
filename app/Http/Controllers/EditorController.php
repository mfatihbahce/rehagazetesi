<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\NewsService;

class EditorController extends Controller
{
    public function __construct(
        protected NewsService $newsService
    ) {}

    /**
     * Editörler listesi
     */
    public function index()
    {
        $editors = User::where('role', 'editor')
            ->with('editorProfile')
            ->whereHas('news', function ($q) {
                $q->where('status', 'published');
            })
            ->get();

        return view('frontend.editors.index', compact('editors'));
    }

    /**
     * Editör detay sayfası
     */
    public function show(int $id)
    {
        $data = $this->newsService->getNewsByAuthor($id);

        return view('frontend.editors.show', $data);
    }
}
