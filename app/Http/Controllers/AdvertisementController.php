<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;

class AdvertisementController extends Controller
{
    public function click(Advertisement $advertisement)
    {
        $isEligible = Advertisement::query()
            ->active()
            ->whereKey($advertisement->id)
            ->exists();

        if (!$isEligible || !$advertisement->target_url) {
            return redirect()->route('home');
        }

        $advertisement->increment('clicks');

        return redirect()->away($advertisement->target_url);
    }
}
