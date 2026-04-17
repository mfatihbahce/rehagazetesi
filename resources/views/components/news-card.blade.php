{{--
  Modern haber kartı - görsel ağırlıklı, okunabilir, responsive
  Varyant: default | withMeta | compact | hero
--}}
@props([
    'news',
    'variant' => 'default',
    'showDate' => false,
    'showViews' => false,
])

@php
    $showDate = $showDate || $variant === 'withMeta';
    $showViews = false; // Frontend'de okunma sayısı geçici olarak gizli
    $isCompact = $variant === 'compact';
    $isHero = $variant === 'hero';
    $imgUrl = $news->featured_image ? asset('storage/'.$news->featured_image) : 'https://via.placeholder.com/800x500/1a1a1a/6b7280?text=Haber';
@endphp

<a href="{{ route('news.show', $news->slug) }}" {{ $attributes->merge(['class' => 'group block h-full']) }}>
    <article class="bg-white rounded-xl overflow-hidden h-full flex flex-col shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-gray-200">
        {{-- Görsel alanı - büyük, 16:9, overlay --}}
        <div class="relative aspect-[16/10] sm:aspect-video overflow-hidden shrink-0">
            <img src="{{ $imgUrl }}"
                alt="{{ $news->title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy">
            {{-- Gradient overlay - metin okunabilirliği --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-60 group-hover:opacity-80 transition-opacity"></div>
            {{-- Kategori badge --}}
            @if($news->category)
            <span class="absolute top-3 left-3 px-2.5 py-1 bg-[#BB0A30] text-white text-xs font-semibold uppercase tracking-wider rounded">
                {{ $news->category->name }}
            </span>
            @endif
            {{-- Mobilde başlık overlay (opsiyonel) - sadece compact'ta --}}
            @if($isCompact)
            <h3 class="absolute bottom-0 left-0 right-0 p-3 text-white font-bold text-sm leading-snug line-clamp-2">
                {{ $news->title }}
            </h3>
            @endif
        </div>

        @if(!$isCompact)
        <div class="p-4 sm:p-5 flex-1 flex flex-col">
            <h3 class="font-bold text-gray-900 text-base sm:text-lg leading-snug group-hover:text-[#BB0A30] transition-colors line-clamp-2 min-h-[2.5rem]">
                {{ $news->title }}
            </h3>
            @if($showDate || $showViews)
            <div class="flex items-center gap-3 mt-3 text-xs text-gray-500 mt-auto pt-3 border-t border-gray-100">
                @if($showDate)
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $news->published_at?->format('d.m.Y') }}
                </span>
                @endif
                @if($showViews)
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    {{ number_format($news->views) }} okunma
                </span>
                @endif
            </div>
            @endif
        </div>
        @endif
    </article>
</a>
