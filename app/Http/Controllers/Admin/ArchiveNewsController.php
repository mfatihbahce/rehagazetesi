<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchiveNews;
use Illuminate\Support\Facades\DB;

class ArchiveNewsController extends Controller
{
    public function index()
    {
        abort_unless(config('archive.enabled'), 404);

        $user = auth()->user();
        abort_if(!$user || !$user->isEditor(), 403);
        abort_if(!$user->can_access_archive || !$user->legacy_user_id, 403);

        $authorKey = config('archive.columns.news.author_key', 'user_id');
        $titleColumn = config('archive.columns.news.title', 'title');
        $slugColumn = config('archive.columns.news.slug', 'slug');
        $statusColumn = config('archive.columns.news.status', 'status');
        $publishedAtColumn = config('archive.columns.news.published_at', 'created_at');
        $primaryKey = config('archive.columns.news.primary_key', 'id');

        $archiveNews = ArchiveNews::query()
            ->where($authorKey, $user->legacy_user_id)
            ->select([
                DB::raw("{$primaryKey} as id"),
                DB::raw("{$titleColumn} as title"),
                DB::raw("{$slugColumn} as slug"),
                DB::raw("{$statusColumn} as status"),
                DB::raw("{$publishedAtColumn} as published_at"),
            ])
            ->orderByDesc($publishedAtColumn)
            ->paginate(30);

        return view('admin.archive-news.index', compact('archiveNews'));
    }
}
