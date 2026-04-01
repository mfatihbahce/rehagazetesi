<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SettingsHelper;
use App\Models\Setting;
use App\Services\DemoDataService;
use App\Services\MediaService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct(
        protected MediaService $mediaService,
        protected DemoDataService $demoDataService
    ) {}
    private function getDefaults(): array
    {
        return [
            'site_name' => config('app.name'),
            'site_slogan' => 'Yerel haberlerin güvenilir kaynağı',
            'site_description' => '',
            'site_keywords' => 'yerel haber, haber, gazete',
            'logo_url' => '',
            'favicon_url' => '',
            'contact_email' => 'info@rehagazetesi.com',
            'contact_phone' => '',
            'contact_address' => '',
            'contact_fax' => '',
            'facebook_url' => '',
            'twitter_url' => '',
            'instagram_url' => '',
            'youtube_url' => '',
            'linkedin_url' => '',
            'meta_title' => '',
            'meta_description' => '',
            'google_analytics_id' => '',
            'footer_text' => '© ' . date('Y') . ' Tüm hakları saklıdır.',
            'kunye_content' => '',
            'uets_info' => '',
            'bik_analytics_code' => '',
            'breaking_news_limit' => 5,
            'news_per_page' => 12,
            'comment_enabled' => false,
            'social_share_enabled' => true,
        ];
    }

    public function index()
    {
        $settings = array_merge($this->getDefaults(), SettingsHelper::all());

        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_slogan' => ['nullable', 'string', 'max:255'],
            'site_description' => ['nullable', 'string'],
            'site_keywords' => ['nullable', 'string', 'max:500'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string'],
            'contact_fax' => ['nullable', 'string', 'max:50'],
            'facebook_url' => ['nullable', 'url', 'max:500'],
            'twitter_url' => ['nullable', 'url', 'max:500'],
            'instagram_url' => ['nullable', 'url', 'max:500'],
            'youtube_url' => ['nullable', 'url', 'max:500'],
            'linkedin_url' => ['nullable', 'url', 'max:500'],
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'google_analytics_id' => ['nullable', 'string', 'max:50'],
            'kunye_content' => ['nullable', 'string'],
            'uets_info' => ['nullable', 'string', 'max:500'],
            'bik_analytics_code' => ['nullable', 'string'],
            'footer_text' => ['nullable', 'string'],
            'breaking_news_limit' => ['nullable', 'integer', 'min:1', 'max:20'],
            'news_per_page' => ['nullable', 'integer', 'min:6', 'max:50'],
            'comment_enabled' => ['nullable', 'boolean'],
            'social_share_enabled' => ['nullable', 'boolean'],
        ]);

        $validated['comment_enabled'] = (bool) ($request->boolean('comment_enabled'));
        $validated['social_share_enabled'] = (bool) ($request->boolean('social_share_enabled'));

        if ($request->hasFile('logo')) {
            $request->validate(['logo' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048']]);
            $validated['logo_url'] = $this->mediaService->uploadSiteLogo($request->file('logo'));
        } else {
            $validated['logo_url'] = SettingsHelper::get('logo_url', '');
        }

        if ($request->hasFile('favicon')) {
            $request->validate(['favicon' => ['file', 'mimes:ico,png,gif', 'max:512']]);
            $validated['favicon_url'] = $this->mediaService->uploadFavicon($request->file('favicon'));
        } else {
            $validated['favicon_url'] = SettingsHelper::get('favicon_url', '');
        }

        Setting::setMany($validated);

        return back()->with('success', 'Ayarlar kaydedildi.');
    }

    public function loadDemoData()
    {
        $stats = $this->demoDataService->load();

        return back()->with(
            'success',
            "Demo veriler yuklendi. Ayar: {$stats['settings']} | Kategori (eklenen/guncellenen): {$stats['categories']}/{$stats['updated_categories']} | Editor (eklenen/guncellenen): {$stats['users']}/{$stats['updated_users']} | Haber (eklenen/guncellenen): {$stats['news']}/{$stats['updated_news']}."
        );
    }

    public function clearDemoData()
    {
        $stats = $this->demoDataService->clear();

        return back()->with(
            'success',
            "Demo veriler temizlendi. Geri Alinan/Silinen -> Ayar: {$stats['settings']}, Kategori: {$stats['categories']}, Editor: {$stats['users']}, Haber: {$stats['news']}."
        );
    }
}
