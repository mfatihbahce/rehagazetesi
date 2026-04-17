@extends('layouts.admin')

@section('title', 'Reklam Alanları')
@section('page-title', 'Reklam Yönetimi')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Toplam Reklam</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Aktif</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['active']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Sol Alan</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['left']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Sağ Alan</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['right']) }}</p>
    </div>
</div>

<div class="mb-4">
    <a href="{{ route('admin.advertisements.create') }}" class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Yeni Reklam Ekle
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left p-4">Reklam</th>
                <th class="text-left p-4">Alan</th>
                <th class="text-left p-4">Tip</th>
                <th class="text-left p-4">Durum</th>
                <th class="text-left p-4">Performans</th>
                <th class="text-left p-4">Öncelik</th>
                <th class="text-right p-4">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @forelse($advertisements as $ad)
            @php
                $ctr = $ad->impressions > 0 ? round(($ad->clicks / $ad->impressions) * 100, 2) : 0;
                $placementLabel = \App\Models\Advertisement::placements()[$ad->placement] ?? $ad->placement;
            @endphp
            <tr class="border-t hover:bg-gray-50 align-top">
                <td class="p-4">
                    <p class="font-medium text-gray-900">{{ $ad->title }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $ad->target_url ?: 'Harici URL yok' }}</p>
                </td>
                <td class="p-4 text-sm">{{ $placementLabel }}</td>
                <td class="p-4 text-sm">{{ $ad->type === 'html' ? 'HTML / Script' : 'Görsel' }}</td>
                <td class="p-4">
                    <span class="inline-flex text-xs px-2 py-1 rounded-full {{ $ad->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-700' }}">
                        {{ $ad->is_active ? 'Aktif' : 'Pasif' }}
                    </span>
                </td>
                <td class="p-4 text-sm">
                    <p>{{ number_format($ad->impressions) }} gösterim</p>
                    <p>{{ number_format($ad->clicks) }} tıklama</p>
                    <p class="text-xs text-gray-500">CTR: %{{ number_format($ctr, 2) }}</p>
                </td>
                <td class="p-4 text-sm">{{ $ad->priority }}</td>
                <td class="p-4 text-right">
                    <div class="inline-flex items-center gap-3">
                        <a href="{{ route('admin.advertisements.edit', $ad) }}" class="text-blue-600 hover:underline">Düzenle</a>
                        <form action="{{ route('admin.advertisements.destroy', $ad) }}" method="POST" class="inline" onsubmit="return confirm('Bu reklamı silmek istediğinize emin misiniz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Sil</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="p-8 text-center text-gray-500">Henüz reklam kaydı bulunmuyor.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $advertisements->links() }}
</div>
@endsection
