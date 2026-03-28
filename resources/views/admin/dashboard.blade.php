@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', $isEditor ? 'Benim Panelim' : 'Dashboard')

@section('content')
{{-- İstatistik Kartları --}}
<section class="mb-8" id="istatistikler">
    @php
        $kpiCards = [
            ['label' => 'Toplam Haber', 'value' => $stats['total_news'], 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
            ['label' => 'Taslak', 'value' => $stats['draft_news'] ?? 0, 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'adminOnly' => true],
            ['label' => 'Onay Bekleyen', 'value' => $stats['pending_news'], 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Yayında', 'value' => $stats['published_news'], 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Editör', 'value' => $stats['total_editors'] ?? 0, 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'adminOnly' => true],
            ['label' => $isEditor ? 'Benim Okunma' : 'Toplam Okunma', 'value' => number_format($stats['total_views'] ?? 0), 'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
            ['label' => 'Medya', 'value' => $mediaCount['total'], 'suffix' => 'dosya', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['label' => 'Kategori', 'value' => $stats['total_categories'] ?? 0, 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'adminOnly' => true],
        ];
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($kpiCards as $card)
            @if(!empty($card['adminOnly']) && $isEditor) @continue @endif
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide truncate">{{ $card['label'] }}</p>
                        <p class="text-xl font-bold text-gray-900 truncate">{{ $card['value'] }}{{ isset($card['suffix']) ? ' ' : '' }}<span class="text-sm font-normal text-gray-500">{{ $card['suffix'] ?? '' }}</span></p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

@if(!$isEditor && $visitors)
{{-- Ziyaretçi İstatistikleri --}}
<section class="mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4">
            <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Ziyaretçi İstatistikleri
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <div class="text-center p-4 rounded-xl bg-slate-50">
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($visitors['unique_today']) }}</p>
                    <p class="text-sm text-slate-600">Bugün (benzersiz)</p>
                    @if($visitors['percent_change'] != 0)
                    <p class="text-xs mt-1 {{ $visitors['percent_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $visitors['percent_change'] >= 0 ? '+' : '' }}{{ $visitors['percent_change'] }}% dün
                    </p>
                    @endif
                </div>
                <div class="text-center p-4 rounded-xl bg-slate-50">
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($visitors['unique_week']) }}</p>
                    <p class="text-sm text-slate-600">Bu hafta (benzersiz)</p>
                </div>
                <div class="text-center p-4 rounded-xl bg-slate-50">
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($visitors['hits_today']) }}</p>
                    <p class="text-sm text-slate-600">Bugün (sayfa görüntüleme)</p>
                </div>
                <div class="text-center p-4 rounded-xl bg-slate-50">
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($visitors['hits_week']) }}</p>
                    <p class="text-sm text-slate-600">Bu hafta (sayfa görüntüleme)</p>
                </div>
            </div>

            {{-- 7 Günlük Line Chart --}}
            @if(!empty($dailyChart))
            <div class="border-t border-gray-100 pt-6 mt-2">
                <h4 class="text-sm font-semibold text-gray-700 mb-4">Son 7 Gün – Benzersiz Ziyaretçi</h4>
                @php
                    $chartData = collect($dailyChart);
                    $maxVal = max(1, $chartData->max('unique'));
                    $points = $chartData->map(function($d, $i) use ($maxVal, $chartData) {
                        $x = $chartData->count() > 1 ? ($i / ($chartData->count() - 1)) * 100 : 50;
                        $y = 100 - (($d['unique'] / $maxVal) * 90);
                        return "{$x},{$y}";
                    })->implode(' ');
                @endphp
                <div class="relative w-full" style="height: 140px;">
                    <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-full block">
                        <defs>
                            <linearGradient id="lineGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#ef4444" stop-opacity="0.3"/>
                                <stop offset="100%" stop-color="#ef4444" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <polygon fill="url(#lineGrad)" points="0,100 {{ $points }} 100,100"/>
                        <polyline fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="{{ $points }}"/>
                    </svg>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-500">
                    @foreach($dailyChart as $d)
                    <span>{{ $d['date'] }} ({{ $d['unique'] }})</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- 3 Kart: Son Eklenen, En Çok Okunan, Onay Bekleyen --}}
<section class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @php
        $cardRowClass = 'py-2.5 px-3 rounded-lg hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0';
        $rowLimit = 7;
    @endphp

    {{-- 1. Son Eklenen Haberler --}}
    <article class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <header class="flex justify-between items-center px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">{{ $isEditor ? 'Benim Son Haberlerim' : 'Son Eklenen Haberler' }}</h3>
            <a href="{{ route('admin.news.index') }}" class="text-sm text-red-600 hover:text-red-800 font-medium">Tümü →</a>
        </header>
        <div class="divide-y divide-gray-50">
            @forelse($recentNews->take($rowLimit) as $item)
            <a href="{{ route('admin.news.edit', $item) }}" class="block {{ $cardRowClass }} group">
                <p class="font-medium text-gray-900 group-hover:text-red-600 line-clamp-1 text-sm">{{ $item->title }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $item->created_at->format('d.m.Y H:i') }} · <span class="inline-flex px-1.5 py-0.5 rounded text-xs {{ $item->status === 'published' ? 'bg-green-100 text-green-800' : ($item->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($item->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-700')) }}">{{ match($item->status) { 'published' => 'Yayında', 'pending' => 'Onay Bekleyen', 'draft' => 'Taslak', 'rejected' => 'Reddedildi', default => $item->status } }}</span></p>
            </a>
            @empty
            <div class="py-8 px-5 text-center text-gray-500 text-sm">{{ $isEditor ? 'Henüz haber yazmadınız.' : 'Henüz haber yok.' }}</div>
            @endforelse
        </div>
    </article>

    {{-- 2. En Çok Okunan Haberler --}}
    <article class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <header class="flex justify-between items-center px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">{{ $isEditor ? 'Benim En Çok Okunanlarım' : 'En Çok Okunan Haberler' }}</h3>
            <a href="{{ route('admin.news.index') }}" class="text-sm text-red-600 hover:text-red-800 font-medium">Tümü →</a>
        </header>
        <div class="divide-y divide-gray-50">
            @forelse($popularNews->take($rowLimit) as $item)
            <a href="{{ route('admin.news.edit', $item) }}" class="block {{ $cardRowClass }} group">
                <p class="font-medium text-gray-900 group-hover:text-red-600 line-clamp-1 text-sm">{{ $item->title }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ number_format($item->views) }} okunma</p>
            </a>
            @empty
            <div class="py-8 px-5 text-center text-gray-500 text-sm">{{ $isEditor ? 'Henüz veri yok.' : 'Henüz veri yok.' }}</div>
            @endforelse
        </div>
    </article>

    {{-- 3. Onay Bekleyen Haberler --}}
    <article class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <header class="flex justify-between items-center px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">{{ $isEditor ? 'Onay Bekleyenlerim' : 'Onay Bekleyen Haberler' }}</h3>
            <a href="{{ route('admin.news.index', ['status' => 'pending']) }}" class="text-sm text-red-600 hover:text-red-800 font-medium">Tümü →</a>
        </header>
        <div class="divide-y divide-gray-50">
            @forelse($pendingNews->take($rowLimit) as $item)
            <a href="{{ route('admin.news.edit', $item) }}" class="block {{ $cardRowClass }} group">
                <p class="font-medium text-gray-900 group-hover:text-red-600 line-clamp-1 text-sm">{{ $item->title }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $item->created_at->format('d.m.Y H:i') }}</p>
            </a>
            @empty
            <div class="py-8 px-5 text-center text-gray-500 text-sm">{{ $isEditor ? 'Onay bekleyen haber yok.' : 'Onay bekleyen haber yok.' }}</div>
            @endforelse
        </div>
    </article>
</section>

@if($isEditor)
<div class="mt-8">
    <a href="{{ route('admin.media.index') }}" class="flex items-center justify-between p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div>
            <h3 class="font-medium text-gray-900">Medya Kütüphanesi</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $mediaCount['total'] }} dosya ({{ $mediaCount['images'] }} görsel, {{ $mediaCount['videos'] }} video)</p>
        </div>
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
</div>
@endif
@endsection
