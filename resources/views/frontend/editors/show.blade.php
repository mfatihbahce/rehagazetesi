@extends('layouts.frontend')

@section('content')
<div class="max-w-4xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-8 mb-8">
        <div class="flex flex-col sm:flex-row items-start gap-6">
            @if($author->editorProfile?->profile_photo)
            <img src="{{ asset('storage/'.$author->editorProfile->profile_photo) }}" alt="{{ $author->name }}" class="w-32 h-32 rounded-full object-cover">
            @else
            <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center shrink-0">
                <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-black">{{ $author->name }}</h1>
                @if($author->editorProfile?->title)
                <p class="text-[#BB0A30] font-medium">{{ $author->editorProfile->title }}</p>
                @endif
                @if($author->editorProfile?->bio)
                <p class="text-gray-600 mt-4">{{ $author->editorProfile->bio }}</p>
                @endif
            </div>
        </div>
    </div>

    <h2 class="text-xl font-bold text-black mb-6 border-b-2 border-[#BB0A30] pb-2 inline-block">{{ $author->name }}'in Haberleri</h2>
    @if(!empty($usesArchiveNews) && $usesArchiveNews)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($news as $item)
        @php
            $archiveBaseUrl = rtrim(config('archive.site_url', 'https://arsiv.rehagazetesi.com'), '/');
            $newsPathPrefix = trim((string) config('archive.news_path_prefix', 'kose-yazilari'), '/');
            $archiveUrl = null;
            $statusRaw = strtolower((string) ($item->status ?? ''));
            $statusLabel = match ($statusRaw) {
                'publish', 'published' => 'Yayinda',
                'draft' => 'Taslak',
                'pending' => 'Beklemede',
                'private' => 'Ozel',
                default => $item->status ?: '-',
            };

            if (!empty($item->slug)) {
                $archiveUrl = $archiveBaseUrl . '/' . ($newsPathPrefix !== '' ? $newsPathPrefix . '/' : '') . ltrim($item->slug, '/') . '/';
            } elseif (!empty($item->guid) && filter_var($item->guid, FILTER_VALIDATE_URL)) {
                $archiveUrl = $item->guid;
            } else {
                $archiveUrl = $archiveBaseUrl . '/?p=' . $item->id;
            }

            $coverImage = null;
            if (!empty($item->featured_image) && filter_var($item->featured_image, FILTER_VALIDATE_URL)) {
                $coverImage = preg_replace('/^http:\/\//i', 'https://', $item->featured_image);
            }
        @endphp
        <a href="{{ $archiveUrl }}" target="_blank" rel="noopener noreferrer" class="group block bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 hover:border-gray-200 transition-all duration-300">
            <div class="aspect-video overflow-hidden bg-gray-100 relative">
                @if($coverImage)
                <img src="{{ $coverImage }}" alt="{{ $item->title ?: 'Başlık yok' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="w-full h-full items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300 text-gray-600 text-sm font-medium px-4 text-center hidden">
                    Gorsel yok
                </div>
                @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300 text-gray-600 text-sm font-medium px-4 text-center">
                    Gorsel yok
                </div>
                @endif
            </div>
            <div class="p-4">
                <h3 class="font-bold text-gray-900 leading-snug group-hover:text-[#BB0A30] transition-colors line-clamp-2">{{ $item->title ?: 'Başlık yok' }}</h3>
                @if(!empty($item->excerpt))
                <p class="text-sm text-gray-600 mt-2 line-clamp-3">{{ Str::limit(strip_tags($item->excerpt), 140) }}</p>
                @endif
                <div class="flex items-center justify-between text-xs text-gray-500 mt-4 pt-3 border-t border-gray-100">
                    <span>{{ $statusLabel }}</span>
                    <span>{{ $item->published_at ?: '-' }}</span>
                </div>
            </div>
        </a>
        @empty
        <p class="col-span-full text-gray-500 py-12 text-center">Bu editörün arşivde yayınlanmış haberi yok.</p>
        @endforelse
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($news as $item)
        <x-news-card :news="$item" />
        @empty
        <p class="col-span-full text-gray-500 py-12 text-center">Bu editörün henüz yayınlanmış haberi yok.</p>
        @endforelse
    </div>
    @endif

    <div class="mt-8">
        {{ $news->links() }}
    </div>
</div>
@endsection
