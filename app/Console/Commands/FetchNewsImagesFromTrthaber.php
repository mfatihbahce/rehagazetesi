<?php

namespace App\Console\Commands;

use App\Helpers\CacheKeys;
use App\Models\News;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FetchNewsImagesFromTrthaber extends Command
{
    protected $signature = 'news:fetch-images-trthaber';

    protected $description = 'TRT Haber sitesinden görsel indirir ve görseli olmayan haberlere atar.';

    private array $urlsToFetch = [
        'https://www.trthaber.com/haber/gundem/',
        'https://www.trthaber.com/haber/ekonomi/',
        'https://www.trthaber.com/haber/spor/',
        'https://www.trthaber.com/haber/saglik/',
        'https://www.trthaber.com/haber/egitim/',
        'https://www.trthaber.com/haber/kultur-sanat/',
        'https://www.trthaber.com/haber/bilim-teknoloji/',
        'https://www.trthaber.com/',
    ];

    public function handle(): int
    {
        $this->info('TRT Haber sayfaları taranıyor...');

        $imageUrls = $this->extractImageUrls();

        if (empty($imageUrls)) {
            $this->error('Görsel URL\'si bulunamadı.');
            return 1;
        }

        $this->info(count($imageUrls) . ' görsel URL\'si bulundu.');

        $newsWithoutImage = News::whereNull('featured_image')
            ->orWhere('featured_image', '')
            ->orderBy('id')
            ->get();

        if ($newsWithoutImage->isEmpty()) {
            $this->info('Görseli olmayan haber yok.');
            return 0;
        }

        $downloaded = [];
        $index = 0;

        foreach ($newsWithoutImage as $news) {
            $url = $imageUrls[$index % count($imageUrls)] ?? null;
            if (!$url) continue;

            $path = $this->downloadImage($url);
            if ($path) {
                $news->update(['featured_image' => $path]);
                $downloaded[] = $news->title;
                $this->line("  ✓ {$news->title}");
            }

            $index++;
        }

        CacheKeys::clearNewsCaches();

        $this->info('');
        $this->info(count($downloaded) . ' habere görsel eklendi.');

        return 0;
    }

    private function extractImageUrls(): array
    {
        $urls = [];
        $seen = [];

        foreach ($this->urlsToFetch as $pageUrl) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'Accept' => 'text/html,application/xhtml+xml',
                    ])
                    ->get($pageUrl);

                if (!$response->successful()) continue;

                $html = $response->body();
                if (preg_match_all('/<img[^>]+src=["\']([^"\']+\.(?:jpg|jpeg|png|webp))[^"\']*["\']/i', $html, $m)) {
                    foreach ($m[1] as $src) {
                        $src = $this->resolveUrl($src, $pageUrl);
                        if ($this->isValidImageUrl($src) && !isset($seen[$src])) {
                            $seen[$src] = true;
                            $urls[] = $src;
                        }
                    }
                }
                if (preg_match_all('/data-src=["\']([^"\']+\.(?:jpg|jpeg|png|webp))[^"\']*["\']/i', $html, $m)) {
                    foreach ($m[1] as $src) {
                        $src = $this->resolveUrl($src, $pageUrl);
                        if ($this->isValidImageUrl($src) && !isset($seen[$src])) {
                            $seen[$src] = true;
                            $urls[] = $src;
                        }
                    }
                }
            } catch (\Throwable $e) {
                $this->warn("Sayfa alınamadı: $pageUrl - " . $e->getMessage());
            }
        }

        return array_values(array_unique($urls));
    }

    private function resolveUrl(string $src, string $base): string
    {
        if (str_starts_with($src, '//')) {
            return 'https:' . $src;
        }
        if (str_starts_with($src, '/')) {
            $parsed = parse_url($base);
            return ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? 'www.trthaber.com') . $src;
        }
        if (!str_starts_with($src, 'http')) {
            return rtrim($base, '/') . '/' . ltrim($src, '/');
        }
        return $src;
    }

    private function isValidImageUrl(string $url): bool
    {
        if (str_contains($url, 'logo') || str_contains($url, 'icon') || str_contains($url, 'avatar')) {
            return false;
        }
        if (str_contains($url, 'pixel') || str_contains($url, '1x1') || str_contains($url, 'placeholder')) {
            return false;
        }
        $size = parse_url($url, PHP_URL_QUERY);
        if ($size && (str_contains((string)$size, 'w=1') || str_contains((string)$size, 'width=1'))) {
            return false;
        }
        return strlen($url) > 20 && strlen($url) < 500;
    }

    private function downloadImage(string $url): ?string
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                ->get($url);

            if (!$response->successful() || empty($response->body())) {
                return null;
            }

            $content = $response->body();
            $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $ext = 'jpg';
            }

            $filename = Str::uuid() . '.' . $ext;
            $path = 'news/' . $filename;

            Storage::disk('public')->put($path, $content);

            return $path;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
