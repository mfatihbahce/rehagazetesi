@extends('layouts.frontend')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Hakkımızda</h1>
    <div class="prose prose-lg max-w-none">
        <p>{{ config('app.name') }}, {{ $description ?? 'yerel haberlerin güvenilir kaynağı' }} olarak hizmet vermektedir. Güncel haberler, son dakika gelişmeleri ve bölgenizdeki olaylara dair kapsamlı haberler sunuyoruz.</p>
        <p>Deneyimli kadromuz ve güçlü haber ağımızla, okurlarımıza en doğru ve güncel bilgileri ulaştırmayı hedefliyoruz.</p>
    </div>
</div>
@endsection
