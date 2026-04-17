@extends('layouts.frontend')

@section('title', $news->title . ' - ' . config('app.name'))
@section('og:title', $news->title)
@section('og:description', Str::limit(strip_tags($news->excerpt ?? $news->content), 160))
@if($news->featured_image)
@section('og:image', asset('storage/'.$news->featured_image))
@endif

@push('head')
@php
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'NewsArticle',
        'headline' => $news->title,
        'datePublished' => ($news->published_at ?? $news->created_at)->toIso8601String(),
        'dateModified' => $news->updated_at->toIso8601String(),
        'publisher' => [
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => url('/'),
        ],
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => url()->current()],
    ];
    if ($news->author) {
        $schema['author'] = ['@type' => 'Person', 'name' => $news->author->name, 'url' => route('editor.show', $news->author->id)];
    }
    if ($news->featured_image) {
        $schema['image'] = asset('storage/' . $news->featured_image);
    }
@endphp
<script type="application/ld+json">@json($schema)</script>
@endpush

@section('content')
<article class="max-w-4xl mx-auto">
    <div class="aspect-video lg:aspect-[21/9] overflow-hidden bg-gray-200 mb-8 rounded-xl shadow-lg">
        <img src="{{ $news->featured_image ? asset('storage/'.$news->featured_image) : 'https://via.placeholder.com/1200x600/e5e7eb/6b7280?text=Haber+Görseli' }}"
            alt="{{ $news->title }}" class="w-full h-full object-cover" loading="eager">
    </div>

    <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
        <a href="{{ route('category.show', $news->category->slug) }}" class="px-3 py-1 bg-[#BB0A30] text-white font-medium">{{ $news->category->name }}</a>
        <span>{{ $news->published_at?->format('d.m.Y H:i') }}</span>
    </div>

    @if(\App\Helpers\SettingsHelper::get('social_share_enabled', true))
    @php
        $shareUrl = url()->current();
        $shareTitle = $news->title;
        $shareEncodedUrl = rawurlencode($shareUrl);
        $shareEncodedTitle = rawurlencode($shareTitle);
        $shareText = rawurlencode($shareTitle . ' ' . $shareUrl);
    @endphp
    <div class="flex items-center gap-2 mb-6 py-3 border-y border-gray-200">
        <span class="text-sm font-medium text-gray-600 mr-2">Paylaş:</span>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareEncodedUrl }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-[#1877F2] hover:text-white transition-colors" title="Facebook'ta Paylaş" aria-label="Facebook'ta Paylaş">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
        </a>
        <a href="https://twitter.com/intent/tweet?url={{ $shareEncodedUrl }}&text={{ $shareEncodedTitle }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-black hover:text-white transition-colors" title="X'te Paylaş" aria-label="X'te Paylaş">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        </a>
        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareEncodedUrl }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-[#0A66C2] hover:text-white transition-colors" title="LinkedIn'de Paylaş" aria-label="LinkedIn'de Paylaş">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
        </a>
        <a href="https://t.me/share/url?url={{ $shareEncodedUrl }}&text={{ $shareEncodedTitle }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-[#0088cc] hover:text-white transition-colors" title="Telegram'da Paylaş" aria-label="Telegram'da Paylaş">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
        </a>
        <a href="https://wa.me/?text={{ $shareText }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-[#25D366] hover:text-white transition-colors" title="WhatsApp ile Paylaş" aria-label="WhatsApp ile Paylaş">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </a>
        <a href="mailto:?subject={{ $shareEncodedTitle }}&body={{ $shareEncodedUrl }}" class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-[#BB0A30] hover:text-white transition-colors" title="E-posta ile Paylaş" aria-label="E-posta ile Paylaş">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </a>
    </div>
    @endif

    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 leading-tight mb-4">{{ $news->title }}</h1>

    @if($news->author)
    <div class="flex items-center gap-3 mb-8 pb-6 border-b">
        <a href="{{ route('editor.show', $news->author->id) }}" class="flex items-center gap-3 hover:text-[#BB0A30]">
            @if($news->author->editorProfile?->profile_photo)
            <img src="{{ asset('storage/'.$news->author->editorProfile->profile_photo) }}" alt="{{ $news->author->name }}" class="w-12 h-12 rounded-full object-cover">
            @else
            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            @endif
            <div>
                <span class="font-semibold">{{ $news->author->name }}</span>
                @if($news->author->editorProfile?->title)
                <span class="text-gray-500 text-sm block">{{ $news->author->editorProfile->title }}</span>
                @endif
            </div>
        </a>
    </div>
    @endif

    <div class="prose prose-lg max-w-none prose-headings:font-bold prose-a:text-[#BB0A30] prose-headings:text-black">
        {!! $news->content !!}
    </div>

    @if($news->tags)
    <div class="mt-8 pt-6 border-t">
        <span class="text-sm text-gray-500">Etiketler: </span>
        @foreach(explode(',', $news->tags) as $tag)
        <span class="inline-block px-2 py-0.5 bg-gray-100 rounded text-sm mr-1">{{ trim($tag) }}</span>
        @endforeach
    </div>
    @endif
</article>

{{-- Related News --}}
@if($relatedNews->isNotEmpty())
<section class="mt-16 border-t border-gray-200 pt-12">
    <h2 class="text-xl font-bold text-gray-900 mb-6 border-b-2 border-[#BB0A30] pb-2 inline-block">İlgili Haberler</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6">
        @foreach($relatedNews as $item)
        <x-news-card :news="$item" :show-date="true" :show-views="true" />
        @endforeach
    </div>
</section>
@endif
@endsection
