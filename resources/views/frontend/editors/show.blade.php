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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($news as $item)
        <x-news-card :news="$item" />
        @empty
        <p class="col-span-full text-gray-500 py-12 text-center">Bu editörün henüz yayınlanmış haberi yok.</p>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $news->links() }}
    </div>
</div>
@endsection
