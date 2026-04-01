@extends('layouts.admin')

@section('title', 'Arşiv Yazılarım')
@section('page-title', 'Arşiv Yazılarım')

@section('content')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left p-4">ID</th>
                <th class="text-left p-4">Başlık</th>
                <th class="text-left p-4">Durum</th>
                <th class="text-left p-4">Tarih</th>
                <th class="text-left p-4">Slug</th>
            </tr>
        </thead>
        <tbody>
            @forelse($archiveNews as $item)
            <tr class="border-t hover:bg-gray-50">
                <td class="p-4">{{ $item->id }}</td>
                <td class="p-4">{{ $item->title ?? '-' }}</td>
                <td class="p-4">{{ $item->status ?? '-' }}</td>
                <td class="p-4">{{ $item->published_at ?? '-' }}</td>
                <td class="p-4">{{ $item->slug ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-500">Arşivde eşleşen yazı bulunamadı.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $archiveNews->links() }}
</div>
@endsection
