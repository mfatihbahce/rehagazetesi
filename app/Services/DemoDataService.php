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
    private const SETTINGS_BACKUP_KEY = 'demo_data_settings_backup';

    public function load(): array
    {
        $dataset = $this->demoDataset();
        $created = [
            'categories' => [],
            'users' => [],
            'profiles' => [],
            'news' => [],
            'settings' => [],
        ];
        $addedCounts = [
            'categories' => 0,
            'users' => 0,
            'news' => 0,
            'settings' => 0,
        ];
        $updatedCounts = [
            'categories' => 0,
            'users' => 0,
            'news' => 0,
        ];

        DB::transaction(function () use (&$created, &$addedCounts, &$updatedCounts, $dataset) {
            $settingsFromDataset = is_array($dataset['settings'] ?? null) ? $dataset['settings'] : [];
            if (!empty($settingsFromDataset)) {
                $settingKeys = array_keys($settingsFromDataset);
                $existingSettings = Setting::query()
                    ->whereIn('key', $settingKeys)
                    ->pluck('value', 'key')
                    ->toArray();

                Setting::setMany($settingsFromDataset);
                $created['settings'] = $settingKeys;
                $addedCounts['settings'] = count($settingKeys);

                Setting::set(self::SETTINGS_BACKUP_KEY, json_encode([
                    'touched_keys' => $settingKeys,
                    'existing_before' => $existingSettings,
                ]));
            }

            foreach ($dataset['categories'] as $row) {
                $existing = Category::where('slug', $row['slug'])->first();
                if ($existing) {
                    $existing->fill([
                        'name' => $row['name'] ?? $existing->name,
                        'description' => $row['description'] ?? $existing->description,
                        'order' => $row['order'] ?? $existing->order,
                        'is_active' => $row['is_active'] ?? $existing->is_active,
                    ])->save();
                    $updatedCounts['categories']++;
                    continue;
                }

                $category = Category::create($row);
                $created['categories'][] = $category->id;
                $addedCounts['categories']++;
            }

            foreach ($dataset['editors'] as $row) {
                $existing = User::where('email', $row['email'])->first();
                if ($existing) {
                    $existing->fill([
                        'name' => $row['name'] ?? $existing->name,
                        'role' => $row['role'] ?? $existing->role,
                        'can_access_archive' => (bool) ($row['can_access_archive'] ?? $existing->can_access_archive),
                        'legacy_user_id' => array_key_exists('legacy_user_id', $row) ? ($row['legacy_user_id'] !== null ? (int) $row['legacy_user_id'] : null) : $existing->legacy_user_id,
                    ])->save();
                    $updatedCounts['users']++;
                    EditorProfile::updateOrCreate(
                        ['user_id' => $existing->id],
                        [
                            'title' => $row['title'] ?? null,
                            'bio' => $row['bio'] ?? null,
                        ]
                    );
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
                $existingNews = News::where('slug', $row['slug'])->first();

                $categoryId = Category::where('slug', $row['category_slug'])->value('id');
                $editorId = User::where('email', $row['editor_email'])->value('id');
                if (!$categoryId && !$existingNews) {
                    continue;
                }
                if (!$editorId && !$existingNews) {
                    continue;
                }

                $payload = [
                    'title' => $row['title'],
                    'slug' => $row['slug'],
                    'excerpt' => $row['excerpt'],
                    'content' => $row['content'],
                    'category_id' => $categoryId ?: $existingNews?->category_id,
                    'user_id' => $editorId ?: $existingNews?->user_id,
                    'status' => $row['status'] ?? 'published',
                    'featured_image' => $row['featured_image'] ?? null,
                    'tags' => $row['tags'] ?? null,
                    'is_breaking' => $row['is_breaking'] ?? false,
                    'is_featured' => $row['is_featured'] ?? false,
                    'views' => (int) ($row['views'] ?? 0),
                    'published_at' => $row['published_at'] ?? now()->subDays($row['days_ago'] ?? 1),
                ];

                if ($existingNews) {
                    $existingNews->fill($payload)->save();
                    $updatedCounts['news']++;
                    continue;
                }

                $news = News::create($payload);

                $created['news'][] = $news->id;
                $addedCounts['news']++;
            }

            $this->persistTrackingIds($created);
        });

        return array_merge($addedCounts, [
            'updated_categories' => $updatedCounts['categories'],
            'updated_users' => $updatedCounts['users'],
            'updated_news' => $updatedCounts['news'],
        ]);
    }

    public function clear(): array
    {
        $tracking = $this->getTrackingIds();
        $deleted = [
            'categories' => 0,
            'users' => 0,
            'news' => 0,
            'settings' => 0,
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

            if (!empty($tracking['settings'])) {
                $backupRaw = Setting::getValue(self::SETTINGS_BACKUP_KEY, '');
                $backup = is_string($backupRaw) ? json_decode($backupRaw, true) : null;
                $existingBefore = is_array($backup['existing_before'] ?? null) ? $backup['existing_before'] : [];
                $touched = is_array($backup['touched_keys'] ?? null) ? $backup['touched_keys'] : $tracking['settings'];

                foreach ($touched as $key) {
                    $key = (string) $key;
                    if (array_key_exists($key, $existingBefore)) {
                        Setting::set($key, (string) $existingBefore[$key]);
                    } else {
                        Setting::query()->where('key', $key)->delete();
                    }
                }
                $deleted['settings'] = count($touched);
                Setting::query()->where('key', self::SETTINGS_BACKUP_KEY)->delete();
            }

            Setting::set(self::TRACKING_KEY, json_encode([
                'categories' => [],
                'users' => [],
                'profiles' => [],
                'news' => [],
                'settings' => [],
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
                'settings' => [],
            ];
        }

        $decoded = json_decode((string) file_get_contents($path), true);
        if (!is_array($decoded)) {
            return [
                'categories' => [],
                'editors' => [],
                'news' => [],
                'settings' => [],
            ];
        }

        return [
            'categories' => is_array($decoded['categories'] ?? null) ? $decoded['categories'] : [],
            'editors' => is_array($decoded['editors'] ?? null) ? $decoded['editors'] : [],
            'news' => is_array($decoded['news'] ?? null) ? $decoded['news'] : [],
            'settings' => is_array($decoded['settings'] ?? null) ? $decoded['settings'] : [],
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
            'settings' => array_values(array_unique(array_merge($old['settings'], $newIds['settings']))),
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
                'settings' => [],
            ];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [
                'categories' => [],
                'users' => [],
                'profiles' => [],
                'news' => [],
                'settings' => [],
            ];
        }

        return [
            'categories' => array_values(array_map('intval', $decoded['categories'] ?? [])),
            'users' => array_values(array_map('intval', $decoded['users'] ?? [])),
            'profiles' => array_values(array_map('intval', $decoded['profiles'] ?? [])),
            'news' => array_values(array_map('intval', $decoded['news'] ?? [])),
            'settings' => array_values(array_map('strval', $decoded['settings'] ?? [])),
        ];
    }

}
