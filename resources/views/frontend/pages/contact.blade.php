@extends('layouts.frontend')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">İletişim</h1>
    <p class="text-gray-600 mb-8">Bizimle iletişime geçmek için aşağıdaki bilgileri kullanabilirsiniz.</p>
    <div class="bg-white border border-gray-200 p-6 space-y-4">
        @if($contact['email'])
        <p><strong>E-posta:</strong> <a href="mailto:{{ $contact['email'] }}" class="text-[#BB0A30] hover:underline">{{ $contact['email'] }}</a></p>
        @endif
        @if($contact['phone'])
        <p><strong>Telefon:</strong> {{ $contact['phone'] }}</p>
        @endif
        @if($contact['address'])
        <p><strong>Adres:</strong> {{ $contact['address'] }}</p>
        @endif
        @if(empty($contact['email']) && empty($contact['phone']) && empty($contact['address']))
        <p class="text-gray-500">İletişim bilgileri henüz eklenmemiş. Admin panelinden Site Ayarları bölümünden ekleyebilirsiniz.</p>
        @endif
    </div>
</div>
@endsection
