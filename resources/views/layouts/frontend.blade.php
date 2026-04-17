<!DOCTYPE html>
<html lang="tr" class="min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription ?? config('app.name') }} - Yerel haber portalı">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>

    <meta property="og:title" content="@yield('og:title', config('app.name'))">
    <meta property="og:description" content="@yield('og:description', config('app.name'))">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('og:image')
    <meta property="og:image" content="@yield('og:image')">
    @endif
    @php $favicon = \App\Helpers\SettingsHelper::get('favicon_url', ''); @endphp
    @if($favicon)
    <link rel="icon" href="{{ str_starts_with($favicon, 'http') ? $favicon : asset('storage/' . $favicon) }}" type="image/x-icon">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>!function(){var e=console.warn;console.warn=function(t){t&&String(t).includes("cdn.tailwindcss.com")||e.apply(console,arguments)}}();</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'] },
                    colors: {
                        trtred: '#BB0A30',
                        primary: '#BB0A30',
                        dark: '#1a1a1a',
                    }
                }
            }
        }
    </script>
    <style>
        body { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        .ticker-wrap { overflow: hidden; white-space: nowrap; }
        .ticker-inner { display: inline-block; animation: ticker 60s linear infinite; }
        .ticker-inner:hover { animation-play-state: paused; }
        @keyframes ticker { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .hero-slider .hero-slide { display: none; transition: opacity 0.4s ease; }
        .hero-slider .hero-slide.active { display: grid; }
        .tags-scroll { -webkit-overflow-scrolling: touch; scrollbar-width: thin; }
        .tags-scroll::-webkit-scrollbar { height: 4px; }
        .mobile-menu-panel { transform: translateX(-100%); transition: transform 0.3s ease; }
        .mobile-menu-panel.open { transform: translateX(0); }
        .mobile-menu-overlay { opacity: 0; visibility: hidden; pointer-events: none; transition: opacity 0.3s, visibility 0.3s; }
        .mobile-menu-overlay.show { opacity: 1; visibility: visible; pointer-events: auto; }
    </style>
    @php $bikCode = \App\Helpers\SettingsHelper::get('bik_analytics_code', ''); @endphp
    @if($bikCode)
    {!! $bikCode !!}
    @endif
    @stack('head')
    @stack('styles')
</head>
<body class="font-sans bg-gray-50 text-gray-900 antialiased min-h-screen flex flex-col">

    {{-- Üst bar - TRT tarzı kırmızı header --}}
    <header class="sticky top-0 z-50">
        <div class="bg-[#BB0A30] text-white">
            <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-14">
                <a href="{{ route('home') }}" class="shrink-0 flex items-center gap-2">
                    @php
                        $logoUrl = \App\Helpers\SettingsHelper::get('logo_url', '');
                        $siteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name'));
                    @endphp
                    @if($logoUrl)
                    <img src="{{ str_starts_with($logoUrl, 'http') ? $logoUrl : asset('storage/' . $logoUrl) }}" alt="{{ $siteName }}" class="h-8 object-contain">
                    @else
                    <span class="font-bold text-xl tracking-tight">{{ $siteName }}</span>
                    @endif
                </a>
                <nav class="hidden lg:flex items-center gap-6 text-sm font-medium uppercase tracking-wide">
                    @foreach(\App\Models\Category::where('is_active', true)->orderBy('order')->limit(5)->get() as $cat)
                    <a href="{{ route('category.show', $cat->slug) }}" class="hover:opacity-90">{{ $cat->name }}</a>
                    @endforeach
                    <a href="{{ route('editors.index') }}" class="hover:opacity-90">Yazarlar</a>
                    <a href="https://arsiv.rehagazetesi.com" class="hover:opacity-90">Arşiv</a>
                </nav>
                <div class="flex items-center gap-4 sm:gap-6 shrink-0">
                    <div class="hidden sm:flex items-center gap-4 text-sm">
                        <a href="{{ route('weather.index') }}" class="flex items-center gap-1.5 hover:opacity-90" title="Hava Durumu - Şanlıurfa">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                            <span id="weather-display">Şanlıurfa —</span>
                        </a>
                        <div class="flex items-center gap-1.5 min-w-[90px]" title="Döviz Kurları">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span id="doviz-display" class="text-xs">Yükleniyor...</span>
                        </div>
                    </div>
                    <form action="{{ route('search') }}" method="GET" class="flex items-center gap-1">
                        <input type="text" name="q" placeholder="Ara…" class="w-24 sm:w-32 py-1.5 px-2 text-sm rounded bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-1 focus:ring-white/50"
                            autocomplete="off" aria-label="Arama">
                        <button type="submit" class="p-1.5 hover:bg-white/10 rounded" aria-label="Ara">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </form>
                </div>
                <button id="mobile-menu-btn" class="lg:hidden p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        {{-- Alt bar - beyaz, hızlı linkler --}}
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 flex items-center justify-between gap-2 py-2 text-sm text-gray-600">
                <div class="tags-scroll flex items-center gap-2 flex-nowrap lg:flex-wrap overflow-x-auto lg:overflow-visible flex-1 lg:flex-initial min-w-0 -mx-1 px-1 lg:mx-0 lg:px-0">
                    @foreach(\App\Models\Category::where('is_active', true)->orderBy('order')->limit(4)->get() as $cat)
                    <a href="{{ route('category.show', $cat->slug) }}" class="px-3 py-1 bg-gray-100 rounded hover:bg-gray-200 whitespace-nowrap shrink-0">#{{ Str::slug($cat->name) }}</a>
                    @endforeach
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    @php $firstCat = \App\Models\Category::where('is_active', true)->orderBy('order')->first(); @endphp
                    @if($firstCat)
                    <a href="{{ route('category.show', $firstCat->slug) }}" class="hover:text-[#BB0A30]">Gündem</a>
                    @endif
                    <a href="{{ route('editors.index') }}" class="hover:text-[#BB0A30]">Yazarlar</a>
                    <a href="https://arsiv.rehagazetesi.com" class="hover:text-[#BB0A30]">Arşiv</a>
                </div>
            </div>
        </div>

        {{-- Son Haberler kayan şerit - TRT tarzı --}}
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 flex items-center gap-0">
                <div class="bg-[#BB0A30] text-white font-bold text-sm uppercase px-4 py-3 shrink-0">Son Haberler</div>
                <div class="flex-1 min-w-0 py-2">
                    @if(isset($breakingNews) && $breakingNews->isNotEmpty())
                    <div class="ticker-wrap">
                        <div class="ticker-inner">
                            @foreach($breakingNews as $news)
                            <a href="{{ route('news.show', $news->slug) }}" class="inline-block mr-10 text-gray-800 hover:text-[#BB0A30]">
                                <span class="text-gray-500 font-mono text-xs mr-2">{{ $news->published_at?->format('H:i') }}</span>
                                {{ $news->title }}
                            </a>
                            @endforeach
                            @foreach($breakingNews as $news)
                            <a href="{{ route('news.show', $news->slug) }}" class="inline-block mr-10 text-gray-800 hover:text-[#BB0A30]">
                                <span class="text-gray-500 font-mono text-xs mr-2">{{ $news->published_at?->format('H:i') }}</span>
                                {{ $news->title }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <p class="text-gray-500 text-sm">Son dakika haberi bulunmuyor.</p>
                    @endif
                </div>
                <a href="{{ route('home') }}#haberler" class="text-[#BB0A30] font-semibold text-sm px-4 shrink-0 hover:underline">Tümü</a>
            </div>
        </div>

        {{-- Mobil menü - soldan sağa açılan panel --}}
        <div id="mobile-menu-overlay" class="mobile-menu-overlay lg:hidden fixed inset-0 bg-black/40 z-40" aria-hidden="true"></div>
        <div id="mobile-menu" class="mobile-menu-panel lg:hidden fixed top-0 left-0 h-full w-72 max-w-[85vw] bg-white shadow-xl z-50 overflow-y-auto">
            <div class="px-4 py-6 flex flex-col gap-1">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-bold text-lg">Menü</span>
                    <button id="mobile-menu-close" class="p-2 -mr-2 hover:bg-gray-100 rounded" aria-label="Menüyü kapat">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('search') }}" method="GET" class="flex items-center gap-2 mb-4">
                    <input type="text" name="q" placeholder="Haber ara…" class="flex-1 min-w-0 border rounded-lg px-4 py-2.5 h-10 text-gray-900">
                    <button type="submit" class="shrink-0 h-10 px-4 bg-[#BB0A30] text-white rounded-lg font-medium flex items-center justify-center">Ara</button>
                </form>
                <a href="{{ route('home') }}" class="py-2 font-medium">Ana Sayfa</a>
                @foreach(\App\Models\Category::where('is_active', true)->orderBy('order')->get() as $cat)
                <a href="{{ route('category.show', $cat->slug) }}" class="py-2">{{ $cat->name }}</a>
                @endforeach
                <a href="{{ route('editors.index') }}" class="py-2">Yazarlar</a>
                <a href="https://arsiv.rehagazetesi.com" class="py-2">Arşiv</a>
                <a href="{{ route('imprint') }}" class="py-2">Künye</a>
            </div>
        </div>
    </header>

    @php
        $layoutAdEnabled = \App\Helpers\SettingsHelper::get('layout_ad_enabled', false);
        $layoutAdDesktop = \App\Helpers\SettingsHelper::get('layout_ad_desktop_url', '');
        $layoutAdMobile = \App\Helpers\SettingsHelper::get('layout_ad_mobile_url', '');
        $layoutAdAlt = \App\Helpers\SettingsHelper::get('layout_ad_alt', '');
    @endphp
    @if($layoutAdEnabled && ($layoutAdDesktop || $layoutAdMobile))
    <section class="bg-transparent" aria-label="Site üst reklam">
        <div class="max-w-7xl mx-auto px-4 py-1">
            @if($layoutAdMobile)
            <div class="lg:hidden">
                <img src="{{ asset('storage/' . $layoutAdMobile) }}" alt="{{ $layoutAdAlt }}" class="w-full h-auto max-h-[120px] object-contain mx-auto">
            </div>
            @endif
            @if($layoutAdDesktop)
            <div class="hidden lg:block">
                <img src="{{ asset('storage/' . $layoutAdDesktop) }}" alt="{{ $layoutAdAlt }}" class="w-full h-auto max-h-[120px] object-contain mx-auto">
            </div>
            @elseif($layoutAdMobile)
            <div class="hidden lg:block">
                <img src="{{ asset('storage/' . $layoutAdMobile) }}" alt="{{ $layoutAdAlt }}" class="w-full h-auto max-h-[120px] object-contain mx-auto">
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- Masaüstü: yan reklamlar fixed değil sticky; böylece footer üzerine binmez, içerikle birlikte yukarı çıkar --}}
    <div class="flex flex-1 flex-col lg:flex-row w-full min-w-0">
        <aside class="hidden lg:block w-[120px] shrink-0 pl-2 box-border" aria-label="Sol reklam alanı">
            <div class="sticky top-[170px] max-h-[calc(100vh-170px-1rem)] overflow-y-auto overflow-x-hidden space-y-3 pr-1">
                @forelse(($leftSidebarAds ?? collect()) as $ad)
                <div class="min-h-[600px] overflow-hidden">
                    @if($ad->type === 'html' && $ad->html_code)
                    {!! $ad->html_code !!}
                    @elseif($ad->image_url)
                    @if($ad->target_url)
                    <a href="{{ route('ads.click', $ad) }}" target="_blank" rel="nofollow sponsored noopener" class="block">
                        <img src="{{ $ad->image_url }}" alt="{{ $ad->alt_text ?: $ad->title }}" class="w-full h-[600px] object-cover">
                    </a>
                    @else
                    <img src="{{ $ad->image_url }}" alt="{{ $ad->alt_text ?: $ad->title }}" class="w-full h-[600px] object-cover">
                    @endif
                    @endif
                </div>
                @empty
                <div class="min-h-[600px] flex items-start justify-center text-[10px] text-gray-400/80 uppercase tracking-wide pt-1">
                    Sol reklam
                </div>
                @endforelse
            </div>
        </aside>

        <main class="flex-1 min-w-0 max-w-7xl mx-auto px-4 py-6 w-full">
            @yield('content')
        </main>

        <aside class="hidden lg:block w-[120px] shrink-0 pr-2 box-border" aria-label="Sağ reklam alanı">
            <div class="sticky top-[170px] max-h-[calc(100vh-170px-1rem)] overflow-y-auto overflow-x-hidden space-y-3 pl-1">
                @forelse(($rightSidebarAds ?? collect()) as $ad)
                <div class="min-h-[600px] overflow-hidden">
                    @if($ad->type === 'html' && $ad->html_code)
                    {!! $ad->html_code !!}
                    @elseif($ad->image_url)
                    @if($ad->target_url)
                    <a href="{{ route('ads.click', $ad) }}" target="_blank" rel="nofollow sponsored noopener" class="block">
                        <img src="{{ $ad->image_url }}" alt="{{ $ad->alt_text ?: $ad->title }}" class="w-full h-[600px] object-cover">
                    </a>
                    @else
                    <img src="{{ $ad->image_url }}" alt="{{ $ad->alt_text ?: $ad->title }}" class="w-full h-[600px] object-cover">
                    @endif
                    @endif
                </div>
                @empty
                <div class="min-h-[600px] flex items-start justify-center text-[10px] text-gray-400/80 uppercase tracking-wide pt-1">
                    Sağ reklam
                </div>
                @endforelse
            </div>
        </aside>
    </div>

    <footer class="mt-16 bg-black text-white">
        <div class="max-w-4xl mx-auto px-4 py-12 text-center">
            {{-- Logo veya site adı --}}
            <a href="{{ route('home') }}" class="inline-block mb-6">
                @php
                    $footerLogoUrl = \App\Helpers\SettingsHelper::get('logo_url', '');
                    $footerSiteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name'));
                @endphp
                @if($footerLogoUrl)
                <img src="{{ str_starts_with($footerLogoUrl, 'http') ? $footerLogoUrl : asset('storage/' . $footerLogoUrl) }}" alt="{{ $footerSiteName }}" class="h-10 object-contain mx-auto">
                @else
                <span class="text-[#BB0A30] font-bold text-2xl tracking-tight">{{ $footerSiteName }}</span>
                @endif
            </a>

            {{-- Navigasyon linkleri --}}
            <nav class="flex flex-wrap justify-center gap-x-4 gap-y-1 mb-8 text-sm">
                <a href="{{ route('home') }}" class="hover:opacity-80 transition-opacity">Ana Sayfa</a>
                <span class="text-gray-600">|</span>
                <a href="{{ route('contact') }}" class="hover:opacity-80 transition-opacity">İletişim</a>
                <span class="text-gray-600">|</span>
                <a href="{{ route('about') }}" class="hover:opacity-80 transition-opacity">Kurumsal</a>
                <span class="text-gray-600">|</span>
                <a href="{{ route('imprint') }}" class="hover:opacity-80 transition-opacity">Künye</a>
                <span class="text-gray-600">|</span>
                <a href="{{ route('editors.index') }}" class="hover:opacity-80 transition-opacity">Yazarlar</a>
                <span class="text-gray-600">|</span>
                <a href="{{ route('weather.index') }}" class="hover:opacity-80 transition-opacity">Hava Durumu</a>
                <span class="text-gray-600">|</span>
                <a href="{{ route('search') }}" class="hover:opacity-80 transition-opacity">Arama</a>
            </nav>

            {{-- Sosyal medya ikonları (Site Ayarlarından) --}}
            @php
                $fb = \App\Helpers\SettingsHelper::get('facebook_url', '');
                $tw = \App\Helpers\SettingsHelper::get('twitter_url', '');
                $ig = \App\Helpers\SettingsHelper::get('instagram_url', '');
                $yt = \App\Helpers\SettingsHelper::get('youtube_url', '');
            @endphp
            <div class="flex justify-center gap-3 mb-8">
                @if($fb)<a href="{{ $fb }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full border-2 border-white/60 flex items-center justify-center hover:border-white hover:bg-white/10 transition-all" aria-label="Facebook"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>@endif
                @if($tw)<a href="{{ $tw }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full border-2 border-white/60 flex items-center justify-center hover:border-white hover:bg-white/10 transition-all" aria-label="Twitter / X"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>@endif
                @if($ig)<a href="{{ $ig }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full border-2 border-white/60 flex items-center justify-center hover:border-white hover:bg-white/10 transition-all" aria-label="Instagram"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg></a>@endif
                @if($yt)<a href="{{ $yt }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full border-2 border-white/60 flex items-center justify-center hover:border-white hover:bg-white/10 transition-all" aria-label="YouTube"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>@endif
            </div>

            {{-- Copyright (Site Ayarları > Görünüm > Footer Metni) --}}
            @php $footerText = \App\Helpers\SettingsHelper::get('footer_text', ''); @endphp
            <p class="text-gray-500 text-sm mb-2">{!! $footerText ? nl2br(e($footerText)) : 'Copyright &copy; ' . date('Y') . '. ' . config('app.name') . '.' !!}</p>
            <p class="text-gray-600 text-xs">Bağlantı yoluyla gidilen dış sitelerin içeriğinden {{ config('app.name') }} sorumlu değildir.</p>
        </div>
    </footer>

    <script>
        (function() {
            var btn = document.getElementById('mobile-menu-btn');
            var menu = document.getElementById('mobile-menu');
            var overlay = document.getElementById('mobile-menu-overlay');
            var closeBtn = document.getElementById('mobile-menu-close');
            function openMenu() {
                menu?.classList.add('open');
                overlay?.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
            function closeMenu() {
                menu?.classList.remove('open');
                overlay?.classList.remove('show');
                document.body.style.overflow = '';
            }
            btn?.addEventListener('click', openMenu);
            closeBtn?.addEventListener('click', closeMenu);
            overlay?.addEventListener('click', closeMenu);
        })();

        (function() {
            var wEl = document.getElementById('weather-display');
            if (wEl) {
                fetch('{{ url("/api/weather/sanliurfa") }}')
                    .then(r => r.json())
                    .then(function(d) {
                        if (d.temp != null)
                            wEl.textContent = 'Şanlıurfa ' + d.temp + '°C';
                        else wEl.textContent = 'Şanlıurfa —';
                    })
                    .catch(function() { if (wEl) wEl.textContent = 'Şanlıurfa —'; });
            }
        })();

        (function() {
            var el = document.getElementById('doviz-display');
            if (!el) return;
            var rates = [], idx = 0;
            fetch('{{ url("/api/rates") }}')
                .then(r => r.json())
                .then(function(data) {
                    if (data.usd) rates.push({ symbol: '$', label: 'USD', rate: data.usd });
                    if (data.eur) rates.push({ symbol: '€', label: 'EUR', rate: data.eur });
                    if (data.gbp) rates.push({ symbol: '£', label: 'GBP', rate: data.gbp });
                    if (data.gold) rates.push({ symbol: 'Au', label: 'Altın', rate: data.gold });
                    if (rates.length) { idx = 0; updateDoviz(); }
                    else el.textContent = '—';
                })
                .catch(function() { el.textContent = '—'; });
            function updateDoviz() {
                if (!rates.length) return;
                var r = rates[idx];
                el.textContent = (r.label === 'Altın' ? 'Gram Altın: ' : '1 ' + r.symbol + ' = ') + r.rate + ' ₺';
                idx = (idx + 1) % rates.length;
            }
            setInterval(function() {
                if (rates.length) { updateDoviz(); }
            }, 4000);
        })();
    </script>
    @stack('scripts')
</body>
</html>
