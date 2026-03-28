@extends('layouts.frontend')

@section('content')
<h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 border-b-2 border-[#BB0A30] pb-2 inline-block">{{ $category->name }}</h1>
@if($category->description)
<p class="text-gray-600 mb-8 max-w-3xl">{{ $category->description }}</p>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 sm:gap-6">
    @forelse($news as $item)
    <x-news-card :news="$item" :show-date="true" :show-views="true" />
    @empty
    <p class="col-span-full text-gray-500 py-12 text-center">Bu kategoride henüz haber bulunmuyor.</p>
    @endforelse
</div>

<div class="mt-8">
    {{ $news->links() }}
</div>
@endsection
