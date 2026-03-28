<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function show(Request $request, ?string $city = null)
    {
        $city = $city ?? config('cities.default');
        $cities = config('cities.cities');
        $cityData = $cities[$city] ?? $cities[config('cities.default')];

        $weather = $this->fetchWeather($cityData['lat'], $cityData['lon']);

        return view('frontend.weather.show', [
            'city' => $city,
            'cityName' => $cityData['name'],
            'cities' => $cities,
            'weather' => $weather,
        ]);
    }

    public function widget(?string $city = null)
    {
        $city = $city ?? config('cities.default');
        $cities = config('cities.cities');
        $cityData = $cities[$city] ?? $cities['sanliurfa'];
        $weather = $this->fetchWeather($cityData['lat'], $cityData['lon'], true);

        $cur = $weather['current'] ?? null;
        $temp = $cur ? round($cur['temperature_2m']) : null;
        $code = $cur['weather_code'] ?? 0;

        return response()->json([
            'city' => $cityData['name'],
            'temp' => $temp,
            'code' => $code,
            'desc' => $temp !== null ? self::weatherCodeToText($code) : null,
        ]);
    }

    private function fetchWeather(float $lat, float $lon, bool $currentOnly = false): ?array
    {
        $key = "weather_{$lat}_{$lon}_" . ($currentOnly ? 'cur' : 'full');
        return Cache::remember($key, 1800, function () use ($lat, $lon, $currentOnly) {
            $params = [
                'latitude' => $lat,
                'longitude' => $lon,
                'current' => 'temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m,wind_direction_10m',
                'timezone' => 'Europe/Istanbul',
            ];
            if (!$currentOnly) {
                $params['daily'] = 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum';
                $params['forecast_days'] = 7;
            }
            $url = 'https://api.open-meteo.com/v1/forecast?' . http_build_query($params);
            $res = Http::timeout(10)->get($url);
            if (!$res->successful()) {
                return null;
            }
            return $res->json();
        });
    }

    public static function weatherCodeToText(int $code): string
    {
        $codes = [
            0 => 'Açık', 1 => 'Çoğunlukla açık', 2 => 'Parçalı bulutlu', 3 => 'Kapalı',
            45 => 'Sis', 48 => 'Kırağılı sis', 51 => 'Hafif çisenti', 53 => 'Çisenti',
            55 => 'Yoğun çisenti', 61 => 'Hafif yağmur', 63 => 'Yağmur', 65 => 'Şiddetli yağmur',
            71 => 'Hafif kar', 73 => 'Kar', 75 => 'Yoğun kar', 77 => 'Kar taneleri',
            80 => 'Hafif sağanak', 81 => 'Sağanak', 82 => 'Şiddetli sağanak',
            95 => 'Gök gürültülü fırtına', 96 => 'Dolu ile fırtına', 99 => 'Şiddetli dolu fırtınası',
        ];
        return $codes[$code] ?? 'Bilinmiyor';
    }
}
