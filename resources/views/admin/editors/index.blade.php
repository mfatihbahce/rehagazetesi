@extends('layouts.admin')

@section('title', 'Editörler')
@section('page-title', 'Editör Listesi')

@section('content')
@if(session('success'))
<div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
@endif
<div class="mb-4">
    <a href="{{ route('admin.editors.create') }}" class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Yeni Editör Ekle
    </a>
</div>
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left p-4">Ad</th>
                <th class="text-left p-4">E-posta</th>
                <th class="text-left p-4">Unvan</th>
                <th class="text-left p-4">Sıra</th>
                <th class="text-left p-4">Baş Köşe</th>
                <th class="text-left p-4">Arşiv ID</th>
                <th class="text-left p-4">Arşiv Erişimi</th>
                <th class="text-left p-4">Haber Sayısı</th>
                <th class="text-right p-4">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @forelse($editors as $editor)
            <tr class="border-t hover:bg-gray-50">
                <td class="p-4 font-medium">{{ $editor->name }}</td>
                <td class="p-4">{{ $editor->email }}</td>
                <td class="p-4">{{ $editor->editorProfile?->title ?? '-' }}</td>
                <td class="p-4">{{ $editor->editor_order ?? '-' }}</td>
                <td class="p-4">{{ $editor->is_chief_columnist ? 'Evet' : 'Hayır' }}</td>
                <td class="p-4">{{ $editor->legacy_user_id ?? '-' }}</td>
                <td class="p-4">{{ $editor->can_access_archive ? 'Açık' : 'Kapalı' }}</td>
                <td class="p-4">{{ $editor->news_count }}</td>
                <td class="p-4 text-right">
                    <div class="inline-flex items-center gap-3">
                        <a href="{{ route('admin.editors.edit', $editor) }}" class="text-red-600 hover:text-red-800 font-medium">Düzenle</a>
                        <form action="{{ route('admin.editors.destroy', $editor) }}" method="POST" class="inline" onsubmit="return confirm('Bu editörü silmek istediğinize emin misiniz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-600 hover:text-red-700 font-medium">Sil</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="p-8 text-center text-gray-500">Editör bulunamadı.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
