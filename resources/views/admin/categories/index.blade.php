@extends('layouts.admin')

@section('title', 'Kategoriler')
@section('page-title', 'Kategori Yönetimi')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.categories.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Yeni Kategori</a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left p-4">Ad</th>
                <th class="text-left p-4">Slug</th>
                <th class="text-left p-4">Sıra</th>
                <th class="text-right p-4">İşlem</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)
            <tr class="border-t hover:bg-gray-50">
                <td class="p-4 font-medium">{{ $cat->name }}</td>
                <td class="p-4 text-gray-500">{{ $cat->slug }}</td>
                <td class="p-4">{{ $cat->order }}</td>
                <td class="p-4 text-right">
                    <a href="{{ route('admin.categories.edit', $cat) }}" class="text-blue-600 hover:underline">Düzenle</a>
                    <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Sil</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="p-8 text-center text-gray-500">Kategori bulunamadı.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
