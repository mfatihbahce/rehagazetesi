<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- TinyMCE düzenleyici iframe'inde sağ tık Yapıştır için pano API (Chrome) --}}
    <meta http-equiv="Permissions-Policy" content="clipboard-read=(self), clipboard-write=(self)">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Inter',sans-serif}
        aside nav::-webkit-scrollbar{display:none}
        aside nav{scrollbar-width:none;-ms-overflow-style:none}
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <div id="admin-sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"></div>
        {{-- Sidebar - Profesyonel haber sitesi paneli --}}
        <aside id="admin-sidebar" class="w-64 bg-slate-800 text-slate-200 fixed h-full flex flex-col z-40 -translate-x-full lg:translate-x-0 transition-transform duration-200">
            <div class="p-5 border-b border-slate-700 shrink-0">
                <div class="flex items-center justify-between gap-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <span class="w-8 h-8 bg-red-600 rounded flex items-center justify-center text-white font-bold text-sm">RG</span>
                    <span class="font-bold text-white text-sm">{{ config('app.name') }}</span>
                </a>
                <button type="button" id="admin-sidebar-close" class="lg:hidden text-slate-300 hover:text-white p-1 rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                {{-- Ana menü --}}
                <div class="px-3 mb-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-2">Genel</p>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-700/50 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-700/50 {{ request()->routeIs('admin.profile.*') ? 'bg-slate-700 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Profil Ayarları</span>
                    </a>
                </div>

                {{-- İçerik / Haberler --}}
                <div class="px-3 mb-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-2">İçerik</p>
                    <a href="{{ route('admin.news.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-700/50 {{ request()->routeIs('admin.news.index') && !request('status') ? 'bg-slate-700 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        <span>@auth{{ auth()->user()->isEditor() ? 'Benim Haberlerim' : 'Tüm Haberler' }}@else Tüm Haberler @endauth</span>
                    </a>
                    <a href="{{ route('admin.news.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-700/50 {{ request()->routeIs('admin.news.create') ? 'bg-slate-700 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Yeni Haber Ekle</span>
                    </a>
                    @if(auth()->user()->isEditor() && auth()->user()->can_access_archive)
                    <a href="{{ route('admin.archive-news.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-700/50 {{ request()->routeIs('admin.archive-news.*') ? 'bg-slate-700 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Arşiv Yazılarım</span>
                    </a>
                    @endif
                    @auth
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.news.index', ['status' => 'pending']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-700/50 {{ request('status') === 'pending' ? 'bg-slate-700 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Onay Bekleyenler</span>
                        @if(isset($pendingNewsCount) && $pendingNewsCount > 0)
                        <span class="ml-auto bg-amber-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">{{ $pendingNewsCount }}</span>
                        @endif
                    </a>
                    @endif
                    @endauth
                </div>

                {{-- Yönetim (sadece admin) --}}
                @auth
                @if(auth()->user()->isAdmin())
                <div class="px-3 mb-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-2">Yönetim</p>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-700/50 {{ request()->routeIs('admin.categories.*') ? 'bg-slate-700 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <span>Kategoriler</span>
                    </a>
                    <a href="{{ route('admin.editors.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-700/50 {{ request()->routeIs('admin.editors.index') ? 'bg-slate-700 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Yazarlar</span>
                    </a>
                    <a href="{{ route('admin.advertisements.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-700/50 {{ request()->routeIs('admin.advertisements.*') ? 'bg-slate-700 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-2v2m0 14v2m-7-9H3m18 0h-2m-1.364-5.636l-1.414 1.414M6.778 17.222l-1.414 1.414m0-12.728l1.414 1.414m10.444 10.444l1.414 1.414M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Reklam Alanları</span>
                    </a>
                </div>
                @endif
                @endauth

                {{-- Medya & Raporlar --}}
                <div class="px-3 mb-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-2">Medya & Rapor</p>
                    <a href="{{ route('admin.dashboard') }}#istatistikler" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-700/50">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>İstatistikler</span>
                    </a>
                    <a href="{{ route('admin.media.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-700/50 {{ request()->routeIs('admin.media.index') ? 'bg-slate-700 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Medya Kütüphanesi</span>
                    </a>
                </div>

                {{-- Ayarlar (sadece admin) --}}
                @auth
                @if(auth()->user()->isAdmin())
                <div class="px-3">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-2">Sistem</p>
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-700/50 {{ request()->routeIs('admin.settings.*') ? 'bg-slate-700 text-white' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Site Ayarları</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}#kunye" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-700/50">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Künye</span>
                    </a>
                </div>
                @endif
                @endauth
            </nav>

            <div class="p-3 border-t border-slate-700 shrink-0">
                <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-2 px-3 py-2 hover:bg-slate-700/50 rounded-lg text-sm">
                    <div class="w-8 h-8 rounded-full bg-slate-600 flex items-center justify-center text-slate-300 text-xs font-medium shrink-0">{{ substr(auth()->user()->name ?? '?', 0, 1) }}</div>
                    <div class="min-w-0 flex-1">
                        <span class="block truncate text-slate-200 font-medium">{{ auth()->user()->name ?? '' }}</span>
                        <span class="block text-xs text-slate-500">{{ auth()->user()->role === 'admin' ? 'Yönetici' : 'Editör' }}</span>
                    </div>
                </a>
            </div>
        </aside>

        <div class="flex-1 lg:ml-64">
            <header class="bg-white shadow-sm px-6 lg:px-8 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-3">
                    <button type="button" id="admin-sidebar-open" class="lg:hidden inline-flex items-center justify-center w-9 h-9 rounded border border-gray-200 text-gray-700 hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Admin Panel')</h1>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" target="_blank" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Siteyi Görüntüle
                    </a>
                    <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 hover:text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Çıkış
                        </button>
                    </form>
                </div>
            </header>

            <main class="p-6 lg:p-8">
                @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
    <script>
        (function() {
            var sidebar = document.getElementById('admin-sidebar');
            var overlay = document.getElementById('admin-sidebar-overlay');
            var openBtn = document.getElementById('admin-sidebar-open');
            var closeBtn = document.getElementById('admin-sidebar-close');
            if (!sidebar || !overlay || !openBtn || !closeBtn) return;

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            openBtn.addEventListener('click', openSidebar);
            closeBtn.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    overlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                } else if (!sidebar.classList.contains('-translate-x-full')) {
                    overlay.classList.remove('hidden');
                }
            });
        })();
    </script>
</body>
</html>
