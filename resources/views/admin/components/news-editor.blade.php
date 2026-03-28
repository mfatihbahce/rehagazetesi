{{-- TinyMCE: jsDelivr CDN (API key gerekmez) --}}
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>
<div id="media-picker-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="font-semibold">Medya Kütüphanesinden Seç</h3>
            <button type="button" onclick="closeMediaPicker()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div class="p-4 flex gap-2">
            <button type="button" onclick="loadMediaPicker('images')" class="px-3 py-1 rounded bg-gray-200">Görseller</button>
            <button type="button" onclick="loadMediaPicker('videos')" class="px-3 py-1 rounded bg-gray-200">Videolar</button>
            <button type="button" onclick="loadMediaPicker()" class="px-3 py-1 rounded bg-gray-200">Tümü</button>
        </div>
        <div id="media-picker-list" class="flex-1 overflow-auto p-4 grid grid-cols-4 sm:grid-cols-6 gap-2"></div>
    </div>
</div>

<div id="video-url-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
        <h3 class="font-semibold mb-4">Video URL ile Ekle (YouTube, Vimeo vb.)</h3>
        <input type="url" id="video-url-input" placeholder="https://www.youtube.com/watch?v=..." class="w-full border rounded-lg px-4 py-2 mb-4">
        <div class="flex gap-2">
            <button type="button" onclick="insertVideoFromUrl()" class="bg-red-600 text-white px-4 py-2 rounded-lg">Ekle</button>
            <button type="button" onclick="closeVideoUrlModal()" class="bg-gray-200 px-4 py-2 rounded-lg">İptal</button>
        </div>
    </div>
</div>

<script>
let mediaPickerCallback = null;
let mediaPickerType = null;

function openMediaPicker(callback, type) {
    mediaPickerCallback = callback;
    mediaPickerType = type || null;
    document.getElementById('media-picker-modal').classList.remove('hidden');
    document.getElementById('media-picker-modal').classList.add('flex');
    loadMediaPicker(mediaPickerType);
}

function closeMediaPicker() {
    document.getElementById('media-picker-modal').classList.add('hidden');
    document.getElementById('media-picker-modal').classList.remove('flex');
}

function loadMediaPicker(type) {
    mediaPickerType = type;
    const url = type ? '{{ route("admin.media.list") }}?type=' + (type === 'images' ? 'images' : type === 'videos' ? 'videos' : '') : '{{ route("admin.media.list") }}';
    fetch(url)
        .then(r => r.json())
        .then(data => {
            const list = document.getElementById('media-picker-list');
            const esc = s => (s||'').replace(/'/g,"\\'");
            list.innerHTML = (data.media || []).map(m => {
                const path = esc(m.path || '');
                if (m.type === 'video') {
                    return `<div class="cursor-pointer border rounded overflow-hidden hover:border-red-500" onclick="selectMedia('${esc(m.url)}', 'video', '${path}')">
                        <video src="${esc(m.url)}" class="w-full aspect-square object-cover" muted></video>
                        <p class="text-xs p-1 truncate">${esc(m.name)}</p>
                    </div>`;
                }
                return `<div class="cursor-pointer border rounded overflow-hidden hover:border-red-500" onclick="selectMedia('${esc(m.url)}', 'image', '${path}')">
                    <img src="${esc(m.url)}" class="w-full aspect-square object-cover" alt="">
                    <p class="text-xs p-1 truncate">${esc(m.name)}</p>
                </div>`;
            }).join('') || '<p class="col-span-full text-gray-500">Medya bulunamadı.</p>';
        });
}

function selectMedia(url, type, path) {
    if (mediaPickerCallback) mediaPickerCallback(url, type, path);
    closeMediaPicker();
}

function openVideoUrlModal(callback) {
    window.videoUrlCallback = callback;
    document.getElementById('video-url-input').value = '';
    document.getElementById('video-url-modal').classList.remove('hidden');
    document.getElementById('video-url-modal').classList.add('flex');
}

function closeVideoUrlModal() {
    document.getElementById('video-url-modal').classList.add('hidden');
    document.getElementById('video-url-modal').classList.remove('flex');
}

function getYoutubeEmbed(url) {
    const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/);
    return match ? `https://www.youtube.com/embed/${match[1]}` : null;
}

function getVimeoEmbed(url) {
    const match = url.match(/vimeo\.com\/(\d+)/);
    return match ? `https://player.vimeo.com/video/${match[1]}` : null;
}

function insertVideoFromUrl() {
    const url = document.getElementById('video-url-input').value.trim();
    if (!url) return;
    let embedUrl = getYoutubeEmbed(url) || getVimeoEmbed(url) || url;
    const html = `<div class="video-embed"><iframe width="560" height="315" src="${embedUrl}" frameborder="0" allowfullscreen></iframe></div>`;
    if (window.videoUrlCallback) window.videoUrlCallback(html);
    closeVideoUrlModal();
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof tinymce === 'undefined') return;

    tinymce.init({
        selector: '#content-editor',
        height: 400,
        menubar: false,
        base_url: 'https://cdn.jsdelivr.net/npm/tinymce@6',
        suffix: '.min',
        plugins: 'lists link code table',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link | customimage medialibrary | videoembed videoupload | code',
        content_style: 'body { font-family: Inter, sans-serif; font-size: 16px; }',
        setup: function(editor) {
            editor.ui.registry.addButton('customimage', {
                text: 'Görsel',
                onAction: function() {
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.accept = 'image/*';
                    input.onchange = function() {
                        const fd = new FormData();
                        fd.append('file', this.files[0]);
                        fd.append('_token', '{{ csrf_token() }}');
                        fd.append('type', 'content');
                        fetch('{{ route("admin.media.upload") }}', { method: 'POST', body: fd })
                            .then(r => r.json())
                            .then(d => { if (d.url) editor.insertContent('<p><img src="'+d.url+'" alt="" style="max-width:100%"/></p>'); });
                    };
                    input.click();
                }
            });
            editor.ui.registry.addButton('medialibrary', {
                text: 'Kütüphane',
                onAction: function() {
                    openMediaPicker(function(url, type) {
                        if (type === 'image') editor.insertContent('<p><img src="'+url+'" alt="" style="max-width:100%"/></p>');
                        else editor.insertContent('<p><video src="'+url+'" controls style="max-width:100%"></video></p>');
                    });
                }
            });
            editor.ui.registry.addButton('videoembed', {
                text: 'Video URL',
                onAction: function() { openVideoUrlModal(function(html) { tinymce.activeEditor.insertContent(html); }); }
            });
            editor.ui.registry.addButton('videoupload', {
                text: 'Video Yükle',
                onAction: function() {
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.accept = 'video/*';
                    input.onchange = function() {
                        const fd = new FormData();
                        fd.append('file', this.files[0]);
                        fd.append('_token', '{{ csrf_token() }}');
                        fd.append('type', 'video');
                        fetch('{{ route("admin.media.upload") }}', { method: 'POST', body: fd })
                            .then(r => r.json())
                            .then(d => { if (d.url) tinymce.activeEditor.insertContent('<p><video src="'+d.url+'" controls style="max-width:100%"></video></p>'); });
                    };
                    input.click();
                }
            });
        }
    });
});
</script>
