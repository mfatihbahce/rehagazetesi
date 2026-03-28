@extends('layouts.admin')

@section('title', 'Haberler')
@section('page-title', 'Haber Yönetimi')

@section('content')
<div class="flex justify-between items-center mb-6">
    <a href="{{ route('admin.news.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Yeni Haber</a>
    <form action="{{ route('admin.news.index') }}" method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ara..." class="border rounded px-3 py-2">
        <select name="status" class="border rounded px-3 py-2">
            <option value="">Tümü</option>
            <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Taslak</option>
            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Onay Bekliyor</option>
            <option value="published" {{ request('status')=='published'?'selected':'' }}>Yayında</option>
            <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Reddedildi</option>
        </select>
        <button type="submit" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">Filtrele</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left p-4">Başlık</th>
                <th class="text-left p-4">Kategori</th>
                <th class="text-left p-4">Yazar</th>
                <th class="text-left p-4">Durum</th>
                <th class="text-left p-4">Tarih</th>
                <th class="text-right p-4">İşlem</th>
            </tr>
        </thead>
        <tbody>
            @forelse($news as $item)
            <tr class="border-t hover:bg-gray-50">
                <td class="p-4">
                    <a href="{{ route('admin.news.edit', $item) }}" class="font-medium hover:text-red-600">{{ Str::limit($item->title, 50) }}</a>
                </td>
                <td class="p-4">{{ $item->category->name }}</td>
                <td class="p-4">{{ $item->author->name }}</td>
                <td class="p-4">
                    @php
                        $statusClass = match($item->status) {
                            'draft' => 'bg-gray-100 text-gray-800',
                            'pending' => 'bg-amber-100 text-amber-800',
                            'published' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <span class="px-2 py-0.5 rounded text-xs {{ $statusClass }}">{{ match($item->status) { 'published' => 'Yayında', 'pending' => 'Onay Bekleyen', 'draft' => 'Taslak', 'rejected' => 'Reddedildi', default => $item->status } }}</span>
                </td>
                <td class="p-4 text-sm text-gray-500">{{ $item->created_at->format('d.m.Y') }}</td>
                <td class="p-4 text-right">
                    <a href="{{ route('admin.news.edit', $item) }}" class="text-blue-600 hover:underline">Düzenle</a>
                    @if(auth()->user()->isAdmin() && $item->status === 'pending')
                    <form action="{{ route('admin.news.approve', $item) }}" method="POST" class="inline ml-2">
                        @csrf
                        <button type="submit" class="text-green-600 hover:underline">Onayla</button>
                    </form>
                    <form action="{{ route('admin.news.reject', $item) }}" method="POST" class="inline ml-2">
                        @csrf
                        <button type="submit" class="text-red-600 hover:underline">Reddet</button>
                    </form>
                    @endif
                    @if(auth()->user()->isAdmin())
                    <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Sil</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="p-8 text-center text-gray-500">Haber bulunamadı.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $news->links() }}
</div>
@endsection
