@extends('layouts.frontend')

@section('content')
{{-- Manşet alanı - 30 saniyede bir öne çıkan haberler döner --}}
@if(isset($featured) && $featured->isNotEmpty())
<section class="mb-12" id="manset">
    <div class="relative bg-white rounded-xl overflow-hidden shadow-lg border border-gray-100 hero-slider">
        @foreach($featured as $index => $main)
        <div class="hero-slide grid grid-cols-1 lg:grid-cols-5 gap-0 {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
            <div class="lg:col-span-3 relative group">
                <a href="{{ route('news.show', $main->slug) }}" class="block aspect-video lg:aspect-[16/9] lg:min-h-[400px] bg-gray-900">
                    <img src="{{ $main->featured_image ? asset('storage/'.$main->featured_image) : 'https://placehold.co/1200x675/1a1a1a/6b7280?text=Haber+Görseli' }}"
                        alt="{{ $main->title }}"
                        class="w-full h-full object-cover object-center group-hover:scale-[1.02] transition-transform duration-500"
                        loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                        onerror="this.src='https://placehold.co/1200x675/1a1a1a/6b7280?text=Haber+Görseli'">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-70"></div>
                    @if($main->category)
                    <span class="absolute top-4 left-4 px-3 py-1.5 bg-[#BB0A30] text-white text-xs font-bold uppercase tracking-wider rounded">
                        {{ $main->category->name }}
                    </span>
                    @endif
                </a>
            </div>
            <div class="lg:col-span-2 flex flex-col p-6 lg:p-8 bg-white">
                <a href="{{ route('news.show', $main->slug) }}" class="group/title">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 leading-tight mb-4 group-hover/title:text-[#BB0A30] transition-colors">
                        {{ $main->title }}
                    </h1>
                </a>
                @if($main->excerpt)
                <p class="text-gray-600 text-base leading-relaxed mb-6 line-clamp-3">
                    {{ Str::limit(strip_tags($main->excerpt), 200) }}
                </p>
                @endif
                <div class="flex items-center gap-4 text-sm text-gray-500 mb-6">
                    <span>{{ $main->published_at?->format('d.m.Y H:i') }}</span>
                    <span>{{ number_format($main->views) }} okunma</span>
                </div>
                <ul class="mt-auto space-y-3 border-t border-gray-100 pt-4">
                    @foreach($featured->except($index)->take(3) as $item)
                    <li>
                        <a href="{{ route('news.show', $item->slug) }}" class="font-semibold text-gray-800 hover:text-[#BB0A30] block line-clamp-2 transition-colors">
                            {{ $item->title }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endforeach
        @if($featured->count() > 1)
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
            @foreach($featured as $i => $m)
            <button type="button" class="hero-dot w-2 h-2 rounded-full transition-colors {{ $i === 0 ? 'bg-[#BB0A30]' : 'bg-gray-300 hover:bg-gray-400' }}" data-index="{{ $i }}" aria-label="Haber {{ $i + 1 }}"></button>
            @endforeach
        </div>
        @endif
    </div>
</section>
@push('scripts')
<script>
(function() {
    var slides = document.querySelectorAll('.hero-slide');
    var dots = document.querySelectorAll('.hero-dot');
    var total = slides.length;
    if (total < 2) return;
    var current = 0;
    function show(n) {
        current = (n + total) % total;
        slides.forEach(function(s, i) { s.classList.toggle('active', i === current); });
        dots.forEach(function(d, i) { d.className = 'hero-dot w-2 h-2 rounded-full transition-colors ' + (i === current ? 'bg-[#BB0A30]' : 'bg-gray-300 hover:bg-gray-400'); });
    }
    setInterval(function() { show(current + 1); }, 5000);
    dots.forEach(function(d) { d.addEventListener('click', function() { show(parseInt(d.dataset.index)); }); });
})();
</script>
@endpush
@endif

{{-- Haber kartları grid - responsive, modern --}}
<section class="mb-12" id="haberler">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 sm:gap-6">
        @php
            $gridNews = $latest->take(8);
            if ($gridNews->isEmpty() && isset($featured)) { $gridNews = $featured->merge($latest)->take(8); }
        @endphp
        @foreach($gridNews as $news)
        <x-news-card :news="$news" :show-date="true" :show-views="true" />
        @endforeach
    </div>
</section>

@if(isset($editors) && $editors->isNotEmpty())
<section class="mb-10" id="editorler-yatay">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-lg sm:text-xl font-bold text-gray-900 border-b-2 border-[#BB0A30] pb-2 inline-block">Editörler</h2>
        <a href="{{ route('editors.index') }}" class="text-[#BB0A30] text-sm font-semibold hover:underline">Tümü →</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        @foreach($editors->take(8) as $editor)
        <a href="{{ route('editor.show', $editor->id) }}" class="group rounded-xl shadow-sm hover:shadow-md transition p-4 flex items-center gap-3 {{ $editor->is_chief_columnist ? 'bg-gradient-to-r from-amber-50 to-white border border-amber-200' : 'bg-white border border-gray-100' }}">
            @if($editor->editorProfile?->profile_photo)
            <img src="{{ asset('storage/'.$editor->editorProfile->profile_photo) }}" alt="{{ $editor->name }}" class="w-12 h-12 rounded-full object-cover shrink-0">
            @else
            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center shrink-0 group-hover:bg-[#BB0A30]/10 transition-colors">
                <svg class="w-6 h-6 text-gray-500 group-hover:text-[#BB0A30]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            @endif
            <div class="min-w-0">
                <span class="font-semibold text-gray-900 group-hover:text-[#BB0A30] block truncate">{{ $editor->name }}</span>
                @if($editor->is_chief_columnist)
                <span class="text-[10px] uppercase tracking-wide bg-amber-100 text-amber-700 px-2 py-0.5 rounded inline-block mt-1">Baş Köşe</span>
                @endif
                @if($editor->editorProfile?->title)
                <span class="text-xs text-gray-500 truncate block">{{ $editor->editorProfile->title }}</span>
                @endif
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif

<div class="flex flex-col lg:flex-row gap-10">
    <div class="flex-1 min-w-0">
        {{-- Kategori blokları --}}
        @foreach($categories as $category)
        @php $catNews = $categoryNews[$category->id] ?? collect(); @endphp
        @if($catNews->isNotEmpty())
        <section class="mb-12">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 border-b-2 border-[#BB0A30] pb-2 inline-block">{{ $category->name }}</h2>
                <a href="{{ route('category.show', $category->slug) }}" class="text-[#BB0A30] text-sm font-semibold hover:underline">Tümü →</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 sm:gap-6">
                @foreach($catNews as $news)
                <x-news-card :news="$news" :show-date="true" :show-views="true" />
                @endforeach
            </div>
        </section>
        @endif
        @endforeach

        @if($categories->isEmpty() && isset($latest) && $latest->isNotEmpty())
        <section class="mb-12">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900 border-b-2 border-[#BB0A30] pb-2 mb-5 inline-block">Son Haberler</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
                @foreach($latest->take(6) as $news)
                <x-news-card :news="$news" :show-date="true" :show-views="true" />
                @endforeach
            </div>
        </section>
        @endif
    </div>

    <aside class="lg:w-80 xl:w-80 shrink-0 space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 lg:sticky lg:top-32">
            <h3 class="font-bold text-gray-900 text-lg mb-5 pb-2 border-b-2 border-[#BB0A30] inline-block">En Çok Okunanlar</h3>
            <ul class="space-y-4">
                @foreach(($popular ?? collect())->take(10) as $index => $news)
                <li>
                    <a href="{{ route('news.show', $news->slug) }}" class="flex gap-3 group">
                        <span class="text-[#BB0A30] font-bold text-sm w-6 shrink-0">{{ $index + 1 }}</span>
                        <span class="group-hover:text-[#BB0A30] font-medium line-clamp-2">{{ $news->title }}</span>
                    </a>
                    <span class="text-xs text-gray-500 block ml-9">{{ $news->views }} okunma</span>
                </li>
                @endforeach
            </ul>
        </div>
    </aside>
</div>
@endsection
