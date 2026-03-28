@extends('layouts.frontend')

@section('title', 'Künye - ' . config('app.name'))

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 border-b-2 border-[#BB0A30] pb-2 inline-block">Künye</h1>
    <div class="prose prose-lg max-w-none text-gray-700">
        @if($content)
            {!! nl2br(e($content)) !!}
        @else
            <p class="text-gray-500">{{ config('app.name') }} künye bilgileri Site Ayarları üzerinden yönetilmektedir.</p>
        @endif
        @php $uets = \App\Helpers\SettingsHelper::get('uets_info', ''); @endphp
        @if($uets)
        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-sm font-medium text-gray-600 mb-1">UETS (Ulusal Elektronik Tebligat Sistemi)</p>
            <p class="text-gray-700">{{ $uets }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
