@extends('layouts.frontend')

@section('title', $query ? "\"{$query}\" Arama Sonuçları - " . config('app.name') : 'Arama - ' . config('app.name'))

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 border-b-2 border-[#BB0A30] pb-2 inline-block">Arama</h1>

    <form action="{{ route('search') }}" method="GET" class="mb-8">
        <div class="flex gap-2">
            <input type="text" name="q" value="{{ old('q', $query) }}" placeholder="Haber, başlık veya metin ara…"
                class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#BB0A30] focus:border-[#BB0A30] outline-none">
            <button type="submit" class="px-6 py-3 bg-[#BB0A30] text-white font-medium rounded-lg hover:bg-[#9d0929]">
                Ara
            </button>
        </div>
    </form>

    <h2 class="text-lg font-semibold text-gray-800 mb-6">
        @if($query)
        "{{ $query }}" için arama sonuçları
        @else
        Aramak istediğiniz kelimeyi yukarıdaki kutuya yazın.
        @endif
    </h2>

@if($query)
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 sm:gap-6">
    @forelse($news as $item)
    <x-news-card :news="$item" :show-date="true" :show-views="true" />
    @empty
    <p class="col-span-full text-gray-500 py-12 text-center">Aramanızla eşleşen haber bulunamadı.</p>
    @endforelse
</div>

<div class="mt-8">
    {{ $news->links() }}
</div>
@else
<p class="text-gray-500">Sonuçları görmek için arama kutusuna en az bir kelime yazıp "Ara" butonuna tıklayın.</p>
@endif
</div>
@endsection
