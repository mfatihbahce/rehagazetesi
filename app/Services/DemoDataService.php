<?php

namespace App\Services;

use App\Models\Category;
use App\Models\EditorProfile;
use App\Models\News;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataService
{
    private const TRACKING_KEY = 'demo_data_tracking_ids';

    public function load(): array
    {
        $dataset = $this->demoDataset();
        $created = [
            'categories' => [],
            'users' => [],
            'profiles' => [],
            'news' => [],
        ];
        $addedCounts = [
            'categories' => 0,
            'users' => 0,
            'news' => 0,
        ];

        DB::transaction(function () use (&$created, &$addedCounts, $dataset) {
            foreach ($dataset['categories'] as $row) {
                $existing = Category::where('slug', $row['slug'])->first();
                if ($existing) {
                    continue;
                }

                $category = Category::create($row);
                $created['categories'][] = $category->id;
                $addedCounts['categories']++;
            }

            foreach ($dataset['editors'] as $row) {
                $existing = User::where('email', $row['email'])->first();
                if ($existing) {
                    continue;
                }

                $user = User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'password' => Hash::make((string) ($row['password'] ?? '123456')),
                    'role' => $row['role'] ?? 'editor',
                    'can_access_archive' => (bool) ($row['can_access_archive'] ?? false),
                    'legacy_user_id' => isset($row['legacy_user_id']) ? (int) $row['legacy_user_id'] : null,
                ]);
                $created['users'][] = $user->id;
                $addedCounts['users']++;

                $profile = EditorProfile::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'title' => $row['title'],
                        'bio' => $row['bio'],
                    ]
                );
                $created['profiles'][] = $profile->id;
            }

            foreach ($dataset['news'] as $row) {
                $exists = News::where('slug', $row['slug'])->exists();
                if ($exists) {
                    continue;
                }

                $categoryId = Category::where('slug', $row['category_slug'])->value('id');
                $editorId = User::where('email', $row['editor_email'])->value('id');
                if (!$categoryId || !$editorId) {
                    continue;
                }

                $news = News::create([
                    'title' => $row['title'],
                    'slug' => $row['slug'],
                    'excerpt' => $row['excerpt'],
                    'content' => $row['content'],
                    'category_id' => $categoryId,
                    'user_id' => $editorId,
                    'status' => $row['status'] ?? 'published',
                    'is_breaking' => $row['is_breaking'] ?? false,
                    'is_featured' => $row['is_featured'] ?? false,
                    'published_at' => $row['published_at'] ?? now()->subDays($row['days_ago'] ?? 1),
                ]);

                $created['news'][] = $news->id;
                $addedCounts['news']++;
            }

            $this->persistTrackingIds($created);
        });

        return $addedCounts;
    }

    public function clear(): array
    {
        $tracking = $this->getTrackingIds();
        $deleted = [
            'categories' => 0,
            'users' => 0,
            'news' => 0,
        ];

        DB::transaction(function () use ($tracking, &$deleted) {
            if (!empty($tracking['news'])) {
                $deleted['news'] = News::whereIn('id', $tracking['news'])->delete();
            }

            if (!empty($tracking['users'])) {
                $deleted['users'] = User::whereIn('id', $tracking['users'])->delete();
            }

            if (!empty($tracking['categories'])) {
                $deleted['categories'] = Category::whereIn('id', $tracking['categories'])->delete();
            }

            Setting::set(self::TRACKING_KEY, json_encode([
                'categories' => [],
                'users' => [],
                'profiles' => [],
                'news' => [],
            ]));
        });

        return $deleted;
    }

    private function demoDataset(): array
    {
        $relativePath = config('demo_data.file', 'database/demo/demo-data.json');
        $path = base_path($relativePath);
        if (!is_file($path)) {
            return [
                'categories' => [],
                'editors' => [],
                'news' => [],
            ];
        }

        $decoded = json_decode((string) file_get_contents($path), true);
        if (!is_array($decoded)) {
            return [
                'categories' => [],
                'editors' => [],
                'news' => [],
            ];
        }

        return [
            'categories' => is_array($decoded['categories'] ?? null) ? $decoded['categories'] : [],
            'editors' => is_array($decoded['editors'] ?? null) ? $decoded['editors'] : [],
            'news' => is_array($decoded['news'] ?? null) ? $decoded['news'] : [],
        ];
    }

    private function persistTrackingIds(array $newIds): void
    {
        $old = $this->getTrackingIds();
        $merged = [
            'categories' => array_values(array_unique(array_merge($old['categories'], $newIds['categories']))),
            'users' => array_values(array_unique(array_merge($old['users'], $newIds['users']))),
            'profiles' => array_values(array_unique(array_merge($old['profiles'], $newIds['profiles']))),
            'news' => array_values(array_unique(array_merge($old['news'], $newIds['news']))),
        ];

        Setting::set(self::TRACKING_KEY, json_encode($merged));
    }

    private function getTrackingIds(): array
    {
        $raw = Setting::getValue(self::TRACKING_KEY, '');
        if (!is_string($raw) || $raw === '') {
            return [
                'categories' => [],
                'users' => [],
                'profiles' => [],
                'news' => [],
            ];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [
                'categories' => [],
                'users' => [],
                'profiles' => [],
                'news' => [],
            ];
        }

        return [
            'categories' => array_values(array_map('intval', $decoded['categories'] ?? [])),
            'users' => array_values(array_map('intval', $decoded['users'] ?? [])),
            'profiles' => array_values(array_map('intval', $decoded['profiles'] ?? [])),
            'news' => array_values(array_map('intval', $decoded['news'] ?? [])),
        ];
    }

}
