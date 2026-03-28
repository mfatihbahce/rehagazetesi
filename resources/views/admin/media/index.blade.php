@extends('layouts.admin')

@section('title', 'Medya Kütüphanesi')
@section('page-title', 'Medya Kütüphanesi')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <p class="text-gray-600 text-sm">Yüklenen görseller ve videolar. Haber formunda medya kütüphanesinden seçebilirsiniz.</p>
    <div class="flex items-center gap-2 flex-wrap">
        <form id="upload-form-image" class="flex items-center gap-2">
            @csrf
            <input type="file" name="file" accept="image/*" class="text-sm">
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">Görsel Yükle</button>
        </form>
        <form id="upload-form-video" class="flex items-center gap-2">
            @csrf
            <input type="hidden" name="type" value="video">
            <input type="file" name="file" accept="video/*" class="text-sm">
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700">Video Yükle</button>
        </form>
    </div>
</div>

<div id="upload-message" class="hidden mb-4 p-4 rounded-lg"></div>

@if($files->isEmpty())
<div class="bg-white rounded-xl shadow p-12 text-center text-gray-500">
    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <p>Henüz yüklenmiş görsel yok.</p>
    <p class="text-sm mt-1">Haber eklerken veya bu sayfadan görsel yükleyebilirsiniz.</p>
</div>
@else
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
    @foreach($files as $file)
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden group">
        <a href="{{ $file['url'] }}" target="_blank" class="relative block aspect-square bg-gray-100 overflow-hidden">
            @if(($file['type'] ?? 'image') === 'video')
            <video src="{{ $file['url'] }}" class="w-full h-full object-cover" muted></video>
            <div class="absolute inset-0 flex items-center justify-center bg-black/30 pointer-events-none">
                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
            @else
            <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" class="w-full h-full object-cover group-hover:opacity-90 transition">
            @endif
        </a>
        <div class="p-2">
            <p class="text-xs text-gray-600 truncate" title="{{ $file['name'] }}">{{ $file['name'] }}</p>
            <p class="text-xs text-gray-400">{{ $file['type'] ?? 'image' }} · {{ $file['folder'] }}</p>
            <input type="text" value="{{ $file['url'] }}" readonly class="mt-1 w-full text-xs border rounded px-2 py-1 bg-gray-50" onclick="this.select()">
        </div>
    </div>
    @endforeach
</div>
@endif

@push('scripts')
<script>
function setupUploadForm(formId) {
    document.getElementById(formId)?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const fileInput = this.querySelector('input[type="file"]');
        const typeInput = this.querySelector('input[name="type"]');
        if (!fileInput.files.length) { alert('Lütfen bir dosya seçin.'); return; }
        const formData = new FormData(this);
    const msg = document.getElementById('upload-message');
    try {
        const res = await fetch('{{ route("admin.media.upload") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        msg.className = 'mb-4 p-4 rounded-lg ' + (data.url ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');
        msg.textContent = data.url ? 'Yükleme başarılı! Sayfa yenileniyor...' : (data.message || 'Hata oluştu.');
        msg.classList.remove('hidden');
        if (data.url) setTimeout(() => location.reload(), 1000);
    } catch (err) {
        msg.className = 'mb-4 p-4 rounded-lg bg-red-100 text-red-700';
        msg.textContent = 'Yükleme hatası.';
        msg.classList.remove('hidden');
    }
    });
}
setupUploadForm('upload-form-image');
setupUploadForm('upload-form-video');
</script>
@endpush
@endsection
