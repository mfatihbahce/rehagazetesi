@extends('layouts.admin')

@section('title', 'Yeni Kategori')
@section('page-title', 'Yeni Kategori Ekle')

@section('content')
<form action="{{ route('admin.categories.store') }}" method="POST" class="w-full">
    @csrf
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Adı *</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded-lg px-4 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Slug (boş bırakılırsa otomatik)</label>
            <input type="text" name="slug" value="{{ old('slug') }}" class="w-full border rounded-lg px-4 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
            <textarea name="description" rows="2" class="w-full border rounded-lg px-4 py-2">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Sıra</label>
            <input type="number" name="order" value="{{ old('order', 0) }}" class="w-full border rounded-lg px-4 py-2">
        </div>
        <div>
            <label class="flex items-center"><input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded"> Aktif</label>
        </div>
    </div>
    <div class="mt-6 flex gap-4">
        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">Kaydet</button>
        <a href="{{ route('admin.categories.index') }}" class="bg-gray-200 px-6 py-2 rounded-lg hover:bg-gray-300">İptal</a>
    </div>
</form>
@endsection
