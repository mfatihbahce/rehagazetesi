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
            ->where('can_access_archive', true)
            ->whereNotNull('legacy_user_id')
            ->orderByDesc('is_chief_columnist')
            ->orderByRaw('CASE WHEN editor_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('editor_order')
            ->orderBy('name')
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
