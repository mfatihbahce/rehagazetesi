@extends('layouts.frontend')

@section('title', $cityName . ' Hava Durumu - ' . config('app.name'))

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 border-b-2 border-[#BB0A30] pb-2 inline-block">Hava Durumu</h1>

    <form action="{{ route('weather.index') }}" method="GET" class="mb-8">
        <label class="block text-sm font-medium text-gray-700 mb-2">İl Seçin</label>
        <div class="flex gap-2 flex-wrap">
            <select name="city" onchange="window.location.href='{{ url('/hava-durumu') }}/'+this.value" class="border rounded-lg px-4 py-2 w-full sm:w-auto">
                @foreach($cities as $slug => $c)
                <option value="{{ $slug }}" {{ $city === $slug ? 'selected' : '' }}>{{ $c['name'] }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if($weather)
    @php
        $cur = $weather['current'] ?? null;
        $daily = $weather['daily'] ?? null;
    @endphp

    {{-- Güncel durum --}}
    @if($cur)
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 sm:p-8 mb-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">{{ $cityName }} - Güncel</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="flex items-center gap-4">
                <div class="text-5xl font-bold text-[#BB0A30]">{{ round($cur['temperature_2m']) }}°C</div>
                <div>
                    <p class="font-medium text-gray-800">{{ \App\Http\Controllers\WeatherController::weatherCodeToText($cur['weather_code']) }}</p>
                    <p class="text-sm text-gray-500">Hissedilen: {{ round($cur['temperature_2m']) }}°C</p>
                </div>
            </div>
            <div class="space-y-2 text-sm">
                <p><span class="text-gray-500">Nem:</span> {{ $cur['relative_humidity_2m'] ?? '—' }}%</p>
                <p><span class="text-gray-500">Rüzgar:</span> {{ $cur['wind_speed_10m'] ?? 0 }} km/s</p>
                @if(!empty($cur['wind_direction_10m']))
                <p><span class="text-gray-500">Yön:</span> {{ $cur['wind_direction_10m'] }}°</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- 7 günlük tahmin --}}
    @if($daily && !empty($daily['time']))
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <h2 class="text-lg font-bold text-gray-900 p-6 pb-4">7 Günlük Tahmin</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-3 font-semibold text-gray-700">Tarih</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Durum</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Min</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Max</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Yağış</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < min(7, count($daily['time'])); $i++)
                    <tr class="border-t border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3">
                            @php
                                $d = \Carbon\Carbon::parse($daily['time'][$i]);
                            @endphp
                            {{ $d->locale('tr')->dayName }}, {{ $d->format('d.m.Y') }}
                        </td>
                        <td class="px-4 py-3">{{ \App\Http\Controllers\WeatherController::weatherCodeToText($daily['weather_code'][$i] ?? 0) }}</td>
                        <td class="px-4 py-3">{{ round($daily['temperature_2m_min'][$i] ?? 0) }}°C</td>
                        <td class="px-4 py-3 font-medium">{{ round($daily['temperature_2m_max'][$i] ?? 0) }}°C</td>
                        <td class="px-4 py-3">{{ ($daily['precipitation_sum'][$i] ?? 0) }} mm</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
    <p class="text-xs text-gray-500 mt-4">Veriler Open-Meteo API üzerinden sağlanmaktadır.</p>
    @endif

    @else
    <p class="text-gray-500 py-8">Hava durumu verisi alınamadı.</p>
    @endif
</div>
@endsection
