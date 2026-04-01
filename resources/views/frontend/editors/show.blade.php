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
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-4">Başlık</th>
                    <th class="text-left p-4">Durum</th>
                    <th class="text-left p-4">Tarih</th>
                </tr>
            </thead>
            <tbody>
                @forelse($news as $item)
                <tr class="border-t">
                    <td class="p-4">
                        @php
                            $archiveBaseUrl = rtrim(config('archive.site_url', 'https://arsiv.rehagazetesi.com'), '/');
                            $newsPathPrefix = trim((string) config('archive.news_path_prefix', 'kose-yazilari'), '/');
                            $archiveUrl = null;

                            if (!empty($item->slug)) {
                                $archiveUrl = $archiveBaseUrl . '/' . ($newsPathPrefix !== '' ? $newsPathPrefix . '/' : '') . ltrim($item->slug, '/') . '/';
                            } elseif (!empty($item->guid) && filter_var($item->guid, FILTER_VALIDATE_URL)) {
                                $archiveUrl = $item->guid;
                            } else {
                                $archiveUrl = $archiveBaseUrl . '/?p=' . $item->id;
                            }
                        @endphp
                        <a href="{{ $archiveUrl }}" target="_blank" rel="noopener noreferrer" class="text-[#BB0A30] hover:underline">
                            {{ $item->title ?: 'Başlık yok' }}
                        </a>
                    </td>
                    <td class="p-4">{{ $item->status ?: '-' }}</td>
                    <td class="p-4">{{ $item->published_at ?: '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="p-8 text-center text-gray-500">Bu editörün arşivde yayınlanmış haberi yok.</td></tr>
                @endforelse
            </tbody>
        </table>
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
