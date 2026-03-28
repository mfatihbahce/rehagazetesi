@extends('layouts.admin')

@section('title', 'Site Ayarları')
@section('page-title', 'Site Ayarları')

@section('content')
<div class="flex flex-col lg:flex-row gap-6">
    {{-- Sol menü --}}
    <nav class="lg:w-56 shrink-0 bg-white rounded-xl shadow overflow-hidden sticky top-24 self-start">
        <div class="p-4 border-b">
            <p class="text-sm font-semibold text-gray-500 uppercase">Ayarlar</p>
        </div>
        <div class="divide-y">
            <a href="#genel" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 text-gray-700">
                <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Genel
            </a>
            <a href="#sosyal" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 text-gray-700">
                <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Sosyal Medya
            </a>
            <a href="#iletisim" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 text-gray-700">
                <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                İletişim
            </a>
            <a href="#seo" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 text-gray-700">
                <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                SEO
            </a>
            <a href="#gorunum" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 text-gray-700">
                <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2V5a2 2 0 00-2-2h-6a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Görünüm
            </a>
            <a href="#yayin" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 text-gray-700">
                <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                Yayın Ayarları
            </a>
            <a href="#kunye" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 text-gray-700">
                <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Künye
            </a>
        </div>
    </nav>

    <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 space-y-6">
        @csrf

        {{-- Genel --}}
        <section id="genel" class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-800">Genel Ayarlar</h3>
                <p class="text-sm text-gray-500">Site kimliği ve temel bilgiler</p>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site Adı *</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" required class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site Sloganı</label>
                    <input type="text" name="site_slogan" value="{{ old('site_slogan', $settings['site_slogan'] ?? '') }}" class="w-full border rounded-lg px-4 py-2" placeholder="Yerel haberlerin güvenilir kaynağı">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site Açıklaması</label>
                    <textarea name="site_description" rows="2" class="w-full border rounded-lg px-4 py-2">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Anahtar Kelimeler</label>
                    <input type="text" name="site_keywords" value="{{ old('site_keywords', $settings['site_keywords'] ?? '') }}" class="w-full border rounded-lg px-4 py-2" placeholder="yerel haber, haber, gazete">
                </div>
            </div>
        </section>

        {{-- Sosyal Medya --}}
        <section id="sosyal" class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-800">Sosyal Medya</h3>
                <p class="text-sm text-gray-500">Sosyal medya hesaplarınız</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Facebook</label><input type="url" name="facebook_url" value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}" class="w-full border rounded-lg px-4 py-2"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Twitter / X</label><input type="url" name="twitter_url" value="{{ old('twitter_url', $settings['twitter_url'] ?? '') }}" class="w-full border rounded-lg px-4 py-2"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Instagram</label><input type="url" name="instagram_url" value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}" class="w-full border rounded-lg px-4 py-2"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">YouTube</label><input type="url" name="youtube_url" value="{{ old('youtube_url', $settings['youtube_url'] ?? '') }}" class="w-full border rounded-lg px-4 py-2"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn</label><input type="url" name="linkedin_url" value="{{ old('linkedin_url', $settings['linkedin_url'] ?? '') }}" class="w-full border rounded-lg px-4 py-2"></div>
                </div>
            </div>
        </section>

        {{-- İletişim --}}
        <section id="iletisim" class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-800">İletişim Bilgileri</h3>
                <p class="text-sm text-gray-500">İletişim sayfasında gösterilecek bilgiler</p>
            </div>
            <div class="p-6 space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label><input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" class="w-full border rounded-lg px-4 py-2"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label><input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" class="w-full border rounded-lg px-4 py-2"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Faks</label><input type="text" name="contact_fax" value="{{ old('contact_fax', $settings['contact_fax'] ?? '') }}" class="w-full border rounded-lg px-4 py-2"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Adres</label><textarea name="contact_address" rows="2" class="w-full border rounded-lg px-4 py-2">{{ old('contact_address', $settings['contact_address'] ?? '') }}</textarea></div>
            </div>
        </section>

        {{-- SEO --}}
        <section id="seo" class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-800">SEO Ayarları</h3>
                <p class="text-sm text-gray-500">Arama motoru optimizasyonu</p>
            </div>
            <div class="p-6 space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Varsayılan Meta Başlık</label><input type="text" name="meta_title" value="{{ old('meta_title', $settings['meta_title'] ?? '') }}" class="w-full border rounded-lg px-4 py-2" maxlength="70" placeholder="En fazla 70 karakter"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Varsayılan Meta Açıklama</label><textarea name="meta_description" rows="2" class="w-full border rounded-lg px-4 py-2" maxlength="160" placeholder="En fazla 160 karakter">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Google Analytics ID</label><input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}" class="w-full border rounded-lg px-4 py-2" placeholder="G-XXXXXXXXXX"></div>
            </div>
        </section>

        {{-- Görünüm --}}
        <section id="gorunum" class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-800">Görünüm</h3>
                <p class="text-sm text-gray-500">Logo, favicon ve metinler</p>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    @if(!empty($settings['logo_url'] ?? ''))
                    <div class="mb-2"><img src="{{ (str_starts_with($settings['logo_url'], 'http') ? $settings['logo_url'] : asset('storage/' . $settings['logo_url'])) }}" alt="Logo" class="h-12 object-contain"></div>
                    @endif
                    <input type="file" name="logo" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full border rounded-lg px-4 py-2 text-sm">
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF veya WebP. Boş bırakırsanız mevcut logo korunur.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                    @if(!empty($settings['favicon_url'] ?? ''))
                    <div class="mb-2"><img src="{{ (str_starts_with($settings['favicon_url'], 'http') ? $settings['favicon_url'] : asset('storage/' . $settings['favicon_url'])) }}" alt="Favicon" class="h-8 w-8 object-contain"></div>
                    @endif
                    <input type="file" name="favicon" accept="image/x-icon,image/png,image/gif" class="w-full border rounded-lg px-4 py-2 text-sm">
                    <p class="mt-1 text-xs text-gray-500">ICO veya PNG. 32x32 önerilir. Boş bırakırsanız mevcut favicon korunur.</p>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Footer Metni</label><textarea name="footer_text" rows="2" class="w-full border rounded-lg px-4 py-2">{{ old('footer_text', $settings['footer_text'] ?? '') }}</textarea></div>
            </div>
        </section>

        {{-- Künye & BIK Uyumluluk --}}
        <section id="kunye" class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-800">Künye Sayfası</h3>
                <p class="text-sm text-gray-500">Künye sayfasında gösterilecek içerik (Yayıncı, Genel Yayın Yönetmeni, İletişim bilgileri vb.)</p>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Künye İçeriği</label>
                    <textarea name="kunye_content" rows="10" class="w-full border rounded-lg px-4 py-2 font-mono text-sm" placeholder="Yayıncı, Genel Yayın Yönetmeni, Adres, Telefon, E-posta...">{{ old('kunye_content', $settings['kunye_content'] ?? '') }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Bu metin /kunye sayfasında gösterilir. Satır sonları korunur.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">UETS Bilgisi (Ulusal Elektronik Tebligat Sistemi)</label>
                    <input type="text" name="uets_info" value="{{ old('uets_info', $settings['uets_info'] ?? '') }}" class="w-full border rounded-lg px-4 py-2" placeholder="UETS sicil no veya elektronik tebligat adresi">
                    <p class="mt-2 text-xs text-gray-500">BIK uyumluluğu için künye sayfasında gösterilir.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">BİK Analitik Ölçüm Kodu</label>
                    <textarea name="bik_analytics_code" rows="6" class="w-full border rounded-lg px-4 py-2 font-mono text-sm" placeholder="BİK tarafından verilen script kodu (&#x3C;script&gt;...&#x3C;/script&gt;)">{{ old('bik_analytics_code', $settings['bik_analytics_code'] ?? '') }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">BİK başvurusu sonrası verilen ölçüm kodu. Tüm frontend sayfalarda head içine eklenir.</p>
                </div>
            </div>
        </section>

        {{-- Yayın Ayarları --}}
        <section id="yayin" class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-800">Yayın Ayarları</h3>
                <p class="text-sm text-gray-500">Haber listeleme ve özellikler</p>
            </div>
            <div class="p-6 space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Son Dakika Limit</label><input type="number" name="breaking_news_limit" value="{{ old('breaking_news_limit', $settings['breaking_news_limit'] ?? 5) }}" class="w-full border rounded-lg px-4 py-2" min="1" max="20"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Sayfa Başına Haber</label><input type="number" name="news_per_page" value="{{ old('news_per_page', $settings['news_per_page'] ?? 12) }}" class="w-full border rounded-lg px-4 py-2" min="6" max="50"></div>
                <div class="flex gap-6">
                    <label class="flex items-center"><input type="checkbox" name="comment_enabled" value="1" {{ old('comment_enabled', $settings['comment_enabled'] ?? false) ? 'checked' : '' }} class="rounded"> Yorumlar Açık</label>
                    <label class="flex items-center"><input type="checkbox" name="social_share_enabled" value="1" {{ old('social_share_enabled', $settings['social_share_enabled'] ?? true) ? 'checked' : '' }} class="rounded"> Sosyal Paylaşım Aktif</label>
                </div>
            </div>
        </section>

        <div class="bg-white rounded-xl shadow p-6">
            <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">Tüm Ayarları Kaydet</button>
            <a href="{{ route('admin.dashboard') }}" class="ml-4 bg-gray-200 px-6 py-2 rounded-lg hover:bg-gray-300 inline-block">İptal</a>
        </div>
    </form>
</div>
@endsection
