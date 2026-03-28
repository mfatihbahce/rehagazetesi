<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RatesController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Cache::remember('doviz_altin', 300, function () {
            $result = ['usd' => null, 'eur' => null, 'gbp' => null, 'gold' => null];

            $forex = Http::timeout(8)
                ->get('https://api.frankfurter.app/latest', ['from' => 'TRY', 'to' => 'USD,EUR,GBP']);
            if ($forex->successful() && $rates = $forex->json('rates')) {
                foreach (['USD' => 'usd', 'EUR' => 'eur', 'GBP' => 'gbp'] as $code => $key) {
                    if (isset($rates[$code]) && $rates[$code] > 0) {
                        $result[$key] = round(1 / $rates[$code], 2);
                    }
                }
            }

            $tcmb = Http::timeout(8)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'])
                ->get('https://www.tcmb.gov.tr/kurlar/today.xml');
            if ($tcmb->successful()) {
                $xml = @simplexml_load_string($tcmb->body());
                if ($xml && isset($xml->Currency)) {
                    foreach ($xml->Currency as $c) {
                        $kod = strtoupper((string) $c['Kod']);
                        $isim = (string) ($c->Isim ?? '');
                        if (in_array($kod, ['XAU', 'GA']) || stripos($isim, 'altın') !== false || stripos($isim, 'gram') !== false) {
                            $buy = (float) ($c->ForexBuying ?? $c->BanknoteBuying ?? $c->CrossRateUSD ?? $c->CrossRateOther ?? 0);
                            if ($buy > 0 && $buy < 100000) {
                                $result['gold'] = round($buy, 2);
                                break;
                            }
                        }
                    }
                }
            }

            return $result;
        });

        return response()->json($data);
    }
}
