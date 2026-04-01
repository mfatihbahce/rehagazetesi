<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\News;
use App\Models\User;
use Illuminate\Console\Command;

class ExportDemoDataFromCurrentDb extends Command
{
    protected $signature = 'demo:export-current {--path= : Output file path relative to project root}';

    protected $description = 'Export current database content to demo dataset JSON';

    public function handle(): int
    {
        $relativePath = $this->option('path') ?: config('demo_data.file', 'database/demo/demo-data.json');
        $outputPath = base_path($relativePath);
        $outputDir = dirname($outputPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0775, true);
        }

        $categories = Category::query()
            ->orderBy('order')
            ->orderBy('id')
            ->get(['name', 'slug', 'description', 'order', 'is_active'])
            ->map(fn (Category $category) => [
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'order' => (int) $category->order,
                'is_active' => (bool) $category->is_active,
            ])
            ->values()
            ->all();

        $users = User::query()
            ->with('editorProfile')
            ->whereIn('role', ['admin', 'editor'])
            ->orderBy('id')
            ->get()
            ->map(fn (User $user) => [
                'name' => $user->name,
                'email' => $user->email,
                'password' => '123456',
                'role' => $user->role,
                'title' => $user->editorProfile?->title,
                'bio' => $user->editorProfile?->bio,
                'legacy_user_id' => $user->legacy_user_id,
                'can_access_archive' => (bool) $user->can_access_archive,
            ])
            ->values()
            ->all();

        $news = News::query()
            ->with(['category:id,slug', 'author:id,email'])
            ->orderBy('id')
            ->get()
            ->filter(fn (News $item) => $item->category && $item->author)
            ->map(fn (News $item) => [
                'title' => $item->title,
                'slug' => $item->slug,
                'excerpt' => $item->excerpt,
                'content' => $item->content,
                'category_slug' => $item->category->slug,
                'editor_email' => $item->author->email,
                'status' => $item->status,
                'featured_image' => $item->featured_image,
                'tags' => $item->tags,
                'is_breaking' => (bool) $item->is_breaking,
                'is_featured' => (bool) $item->is_featured,
                'views' => (int) $item->views,
                'published_at' => optional($item->published_at)->format('Y-m-d H:i:s'),
            ])
            ->values()
            ->all();

        $dataset = [
            'categories' => $categories,
            'editors' => $users,
            'news' => $news,
        ];

        file_put_contents(
            $outputPath,
            json_encode($dataset, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $this->info("Demo dataset exported: {$relativePath}");
        $this->line('Categories: ' . count($categories));
        $this->line('Editors/Admins: ' . count($users));
        $this->line('News: ' . count($news));

        return self::SUCCESS;
    }
}
