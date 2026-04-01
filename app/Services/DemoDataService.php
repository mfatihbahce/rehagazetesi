<?php

namespace App\Services;

use App\Models\Category;
use App\Models\EditorProfile;
use App\Models\News;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataService
{
    private const TRACKING_KEY = 'demo_data_tracking_ids';

    public function load(): array
    {
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

        DB::transaction(function () use (&$created, &$addedCounts) {
            foreach ($this->categoriesPayload() as $row) {
                $existing = Category::where('slug', $row['slug'])->first();
                if ($existing) {
                    continue;
                }

                $category = Category::create($row);
                $created['categories'][] = $category->id;
                $addedCounts['categories']++;
            }

            foreach ($this->editorsPayload() as $row) {
                $existing = User::where('email', $row['email'])->first();
                if ($existing) {
                    continue;
                }

                $user = User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'password' => Hash::make('123456'),
                    'role' => 'editor',
                    'can_access_archive' => false,
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

            foreach ($this->newsPayload() as $row) {
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
                    'status' => 'published',
                    'is_breaking' => $row['is_breaking'] ?? false,
                    'is_featured' => $row['is_featured'] ?? false,
                    'published_at' => now()->subDays($row['days_ago'] ?? 1),
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

    private function categoriesPayload(): array
    {
        return [
            ['name' => 'Gundem', 'slug' => 'gundem', 'description' => 'Guncel gelismeler', 'order' => 1, 'is_active' => true],
            ['name' => 'Spor', 'slug' => 'spor', 'description' => 'Spor haberleri', 'order' => 2, 'is_active' => true],
            ['name' => 'Ekonomi', 'slug' => 'ekonomi', 'description' => 'Ekonomi haberleri', 'order' => 3, 'is_active' => true],
        ];
    }

    private function editorsPayload(): array
    {
        return [
            [
                'name' => 'Demo Editor 1',
                'email' => 'demo.editor1@rehagazetesi.local',
                'title' => 'Muhabir',
                'bio' => 'Demo amacli olusturulan editor hesabi.',
            ],
            [
                'name' => 'Demo Editor 2',
                'email' => 'demo.editor2@rehagazetesi.local',
                'title' => 'Yazar',
                'bio' => 'Panel testleri icin olusturulan demo yazar.',
            ],
        ];
    }

    private function newsPayload(): array
    {
        return [
            [
                'title' => 'Demo Gundem Haberi',
                'slug' => Str::slug('Demo Gundem Haberi'),
                'excerpt' => 'Demo gundem haberi ozeti.',
                'content' => 'Bu icerik admin panel testleri icin otomatik olusturulmustur.',
                'category_slug' => 'gundem',
                'editor_email' => 'demo.editor1@rehagazetesi.local',
                'is_breaking' => true,
                'days_ago' => 1,
            ],
            [
                'title' => 'Demo Spor Analizi',
                'slug' => Str::slug('Demo Spor Analizi'),
                'excerpt' => 'Demo spor haberi ozeti.',
                'content' => 'Bu spor icerigi sadece demo ve test amaciyla eklenmistir.',
                'category_slug' => 'spor',
                'editor_email' => 'demo.editor2@rehagazetesi.local',
                'days_ago' => 2,
            ],
        ];
    }
}
