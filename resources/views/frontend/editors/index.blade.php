@extends('layouts.frontend')

@section('content')
<h1 class="text-2xl lg:text-3xl font-bold text-black mb-8 border-b-2 border-[#BB0A30] pb-2 inline-block">Editörlerimiz</h1>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
    @forelse($editors as $editor)
    <a href="{{ route('editor.show', $editor->id) }}" class="group block rounded-xl shadow hover:shadow-lg transition p-6 {{ $editor->is_chief_columnist ? 'bg-gradient-to-br from-amber-50 to-white border border-amber-200' : 'bg-white' }}">
        <div class="flex items-start gap-4">
            @if($editor->editorProfile?->profile_photo)
            <img src="{{ asset('storage/'.$editor->editorProfile->profile_photo) }}" alt="{{ $editor->name }}" class="w-20 h-20 rounded-full object-cover">
            @else
            <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center shrink-0">
                <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            @endif
            <div>
                <h3 class="font-bold text-lg group-hover:text-[#BB0A30]">{{ $editor->name }}</h3>
                <div class="flex items-center gap-2 flex-wrap mt-1">
                    @if($editor->is_chief_columnist)
                    <span class="text-[10px] uppercase tracking-wide bg-amber-100 text-amber-700 px-2 py-0.5 rounded">Baş Köşe Yazarı</span>
                    @endif
                    @if($editor->editorProfile?->title)
                    <span class="text-[11px] text-gray-500">{{ $editor->editorProfile->title }}</span>
                    @endif
                    @if($editor->editor_order)
                    <span class="text-[10px] uppercase tracking-wide bg-gray-100 text-gray-600 px-2 py-0.5 rounded">Sıra: {{ $editor->editor_order }}</span>
                    @endif
                </div>
                @if($editor->editorProfile?->bio)
                <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ Str::limit($editor->editorProfile->bio, 100) }}</p>
                @endif
            </div>
        </div>
    </a>
    @empty
    <p class="col-span-full text-gray-500 py-12 text-center">Henüz editör bulunmuyor.</p>
    @endforelse
</div>
@endsection
