@php
    $isEdit = isset($advertisement);
@endphp

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 bg-white rounded-xl shadow p-6 space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Reklam Adı *</label>
            <input type="text" name="title" value="{{ old('title', $advertisement->title ?? '') }}" required class="w-full border rounded-lg px-4 py-2">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gösterim Alanı *</label>
                <select name="placement" id="ad-placement" required class="w-full border rounded-lg px-4 py-2">
                    @foreach($placements as $key => $label)
                    <option value="{{ $key }}" @selected(old('placement', $advertisement->placement ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Reklam Tipi *</label>
                <select name="type" id="ad-type" required class="w-full border rounded-lg px-4 py-2">
                    <option value="image" @selected(old('type', $advertisement->type ?? 'image') === 'image')>Görsel Reklam</option>
                    <option value="html" @selected(old('type', $advertisement->type ?? '') === 'html')>HTML / Script Reklam</option>
                </select>
            </div>
        </div>

        <div id="image-fields" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Reklam Görseli {{ $isEdit ? '' : '*' }}</label>
                <input type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,.gif" class="w-full border rounded-lg px-4 py-2 bg-white">
                <p class="text-xs text-gray-500 mt-1">Desteklenen formatlar: JPG, PNG, WEBP, GIF. Maksimum dosya: 4MB.</p>
                @if(!empty($advertisement?->image_url))
                <div class="mt-3 inline-block border border-gray-200 rounded-lg p-2 bg-gray-50">
                    <p class="text-xs text-gray-500 mb-2">Mevcut görsel</p>
                    <img src="{{ $advertisement->image_url }}" alt="{{ $advertisement->alt_text ?: $advertisement->title }}" class="max-w-[180px] h-auto rounded">
                </div>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mobil Görsel {{ $isEdit ? '' : '*' }}</label>
                <input type="file" name="mobile_image_file" accept=".jpg,.jpeg,.png,.webp,.gif" class="w-full border rounded-lg px-4 py-2 bg-white">
                <p class="text-xs text-gray-500 mt-1">Mobilde yatay gösterim için ayrı görsel yükleyin.</p>
                @if(!empty($advertisement?->mobile_image_url))
                <div class="mt-3 inline-block border border-gray-200 rounded-lg p-2 bg-gray-50">
                    <p class="text-xs text-gray-500 mb-2">Mevcut mobil görsel</p>
                    <img src="{{ $advertisement->mobile_image_url }}" alt="{{ $advertisement->alt_text ?: $advertisement->title }}" class="max-w-[220px] h-auto rounded">
                </div>
                @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Yönlendirme URL</label>
                    <input type="url" name="target_url" value="{{ old('target_url', $advertisement->target_url ?? '') }}" class="w-full border rounded-lg px-4 py-2" placeholder="https://...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alt Metin</label>
                    <input type="text" name="alt_text" value="{{ old('alt_text', $advertisement->alt_text ?? '') }}" class="w-full border rounded-lg px-4 py-2" placeholder="Reklam açıklaması">
                </div>
            </div>
        </div>

        <div id="html-fields" class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">HTML / Script Kodu *</label>
            <textarea name="html_code" rows="8" class="w-full border rounded-lg px-4 py-2 font-mono text-sm" placeholder="<script>...</script>">{{ old('html_code', $advertisement->html_code ?? '') }}</textarea>
            <p class="text-xs text-gray-500">Güvenli scriptleri kullanın. Bu alan ham HTML olarak render edilir.</p>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm font-semibold text-blue-900 mb-2">Reklam Tasarım Ölçüleri</p>
            <ul id="ad-size-hints" class="text-sm text-blue-900 space-y-1">
                <li><strong>Sol Reklam Alanı:</strong> 120 x 600 px (önerilen)</li>
                <li><strong>Alternatif Dikey:</strong> 120 x 750 px</li>
                <li><strong>Mobil Yatay:</strong> 640 x 180 px (önerilen)</li>
                <li><strong>Format:</strong> JPG / PNG / WEBP</li>
            </ul>
            <p class="text-xs text-blue-700 mt-2">Not: Sol ve sağ sütunlar 120px genişlikte render edilir.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 space-y-5 h-fit">
        <h3 class="font-semibold text-gray-800">Yayın Ayarları</h3>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Öncelik</label>
            <input type="number" name="priority" value="{{ old('priority', $advertisement->priority ?? 0) }}" min="0" max="9999" class="w-full border rounded-lg px-4 py-2">
            <p class="text-xs text-gray-500 mt-1">Yüksek değerli reklamlar üstte gösterilir.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç Tarihi</label>
            <input type="datetime-local" name="starts_at" value="{{ old('starts_at', isset($advertisement->starts_at) ? $advertisement->starts_at->format('Y-m-d\\TH:i') : '') }}" class="w-full border rounded-lg px-4 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş Tarihi</label>
            <input type="datetime-local" name="ends_at" value="{{ old('ends_at', isset($advertisement->ends_at) ? $advertisement->ends_at->format('Y-m-d\\TH:i') : '') }}" class="w-full border rounded-lg px-4 py-2">
        </div>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" class="rounded" @checked(old('is_active', $advertisement->is_active ?? true))>
            <span class="text-sm font-medium text-gray-700">Aktif</span>
        </label>

        @if($isEdit)
        <div class="pt-3 border-t text-sm text-gray-600 space-y-1">
            <p><span class="font-medium">Gösterim:</span> {{ number_format($advertisement->impressions) }}</p>
            <p><span class="font-medium">Tıklama:</span> {{ number_format($advertisement->clicks) }}</p>
        </div>
        @endif
    </div>
</div>

@if($errors->any())
<div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@push('scripts')
<script>
    (function() {
        var typeSelect = document.getElementById('ad-type');
        var placementSelect = document.getElementById('ad-placement');
        var imageFields = document.getElementById('image-fields');
        var htmlFields = document.getElementById('html-fields');
        var sizeHints = document.getElementById('ad-size-hints');
        if (!typeSelect || !placementSelect || !imageFields || !htmlFields || !sizeHints) return;

        function toggleFields() {
            var isImage = typeSelect.value === 'image';
            imageFields.style.display = isImage ? 'block' : 'none';
            htmlFields.style.display = isImage ? 'none' : 'block';
        }

        typeSelect.addEventListener('change', toggleFields);
        toggleFields();

        function updateSizeHints() {
            var label = placementSelect.value === 'right_sidebar' ? 'Sağ Reklam Alanı' : 'Sol Reklam Alanı';
            sizeHints.innerHTML =
                '<li><strong>' + label + ':</strong> 120 x 600 px (önerilen)</li>' +
                '<li><strong>Alternatif Dikey:</strong> 120 x 750 px</li>' +
                '<li><strong>Mobil Yatay:</strong> 640 x 180 px (önerilen)</li>' +
                '<li><strong>Format:</strong> JPG / PNG / WEBP</li>';
        }

        placementSelect.addEventListener('change', updateSizeHints);
        updateSizeHints();
    })();
</script>
@endpush
