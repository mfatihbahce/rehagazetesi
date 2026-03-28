@extends('layouts.admin')

@section('title', 'Haber Düzenle')
@section('page-title', 'Haber Düzenle')

@section('content')
<form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="news-form">
    @csrf
    @method('PUT')
    <div class="bg-white rounded-xl shadow p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Başlık *</label>
                <input type="text" name="title" value="{{ old('title', $news->title) }}" required class="w-full border rounded-lg px-4 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $news->slug) }}" class="w-full border rounded-lg px-4 py-2">
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Özet</label>
            <textarea name="excerpt" rows="2" class="w-full border rounded-lg px-4 py-2">{{ old('excerpt', $news->excerpt) }}</textarea>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
            <select name="category_id" required class="w-full border rounded-lg px-4 py-2">
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $news->category_id)==$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Öne Çıkan Görsel</label>
            <div class="flex flex-wrap gap-4 items-start">
                <div class="flex-1 min-w-0">
                    @if($news->featured_image)
                    <p class="text-sm text-gray-500 mb-2">Mevcut: <img src="{{ asset('storage/'.$news->featured_image) }}" alt="" class="inline h-12 rounded border"></p>
                    @endif
                    <input type="file" name="featured_image" id="featured_image" accept="image/*" class="w-full border rounded-lg px-4 py-2">
                </div>
                <button type="button" onclick="openMediaPickerForFeatured()" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-sm">Medya Kütüphanesinden Seç</button>
                <input type="hidden" name="featured_image_path" id="featured_image_path" value="">
            </div>
        </div>

        @if(auth()->user()->isAdmin())
        <div class="mt-4 flex gap-6 flex-wrap">
            <label class="flex items-center"><input type="checkbox" name="is_breaking" value="1" {{ old('is_breaking', $news->is_breaking) ? 'checked' : '' }} class="rounded"> Son Dakika</label>
            <label class="flex items-center"><input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $news->is_featured) ? 'checked' : '' }} class="rounded"> Öne Çıkan</label>
            <div>
                <label class="text-sm font-medium">Yayın Durumu:</label>
                <select name="status" class="ml-2 border rounded px-2 py-1">
                    <option value="draft" {{ old('status', $news->status)=='draft' ? 'selected' : '' }}>Taslak</option>
                    <option value="pending" {{ old('status', $news->status)=='pending' ? 'selected' : '' }}>Onay Bekliyor</option>
                    <option value="published" {{ old('status', $news->status)=='published' ? 'selected' : '' }}>Yayında</option>
                    <option value="rejected" {{ old('status', $news->status)=='rejected' ? 'selected' : '' }}>Reddedildi</option>
                </select>
            </div>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">İçerik *</label>
        <textarea name="content" id="content-editor" class="w-full border rounded-lg px-4 py-2">{{ old('content', $news->content) }}</textarea>
    </div>

    @include('admin.components.news-editor')

    <div class="bg-white rounded-xl shadow p-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Etiketler</label>
        <input type="text" name="tags" value="{{ old('tags', $news->tags) }}" class="w-full border rounded-lg px-4 py-2">
    </div>

    <div class="flex gap-4">
        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">Güncelle</button>
        <a href="{{ route('admin.news.index') }}" class="bg-gray-200 px-6 py-2 rounded-lg hover:bg-gray-300">İptal</a>
    </div>
</form>

<script>
function openMediaPickerForFeatured() {
    openMediaPicker(function(url, type, path) {
        if (type === 'image') {
            const p = path || (url.match(/\/storage\/(.+)$/)?.[1] || '');
            document.getElementById('featured_image_path').value = p;
            document.getElementById('featured_image').value = '';
            const preview = document.getElementById('featured-preview');
            if (!preview) { const el = document.createElement('div'); el.id='featured-preview'; el.className='mt-2'; document.getElementById('featured_image').parentNode.appendChild(el); }
            const el = document.getElementById('featured-preview');
            if (el) { el.innerHTML = '<img src="'+url+'" class="max-h-32 rounded border" alt="">'; el.classList.add('mt-2'); }
        }
    }, 'images');
}
document.getElementById('news-form')?.addEventListener('submit', function() {
    if (typeof tinymce !== 'undefined' && tinymce.get('content-editor')) tinymce.get('content-editor').save();
});
</script>
@endsection
