<?php

namespace App\Http\Controllers;

use App\Helpers\SettingsHelper;

class PageController extends Controller
{
    /**
     * Künye sayfası
     */
    public function imprint()
    {
        $content = SettingsHelper::get('kunye_content', '');
        return view('frontend.pages.imprint', compact('content'));
    }

    /**
     * Hakkında sayfası
     */
    public function about()
    {
        $description = SettingsHelper::get('site_description', 'Yerel haberlerin güvenilir kaynağı.');
        return view('frontend.pages.about', compact('description'));
    }

    /**
     * İletişim sayfası
     */
    public function contact()
    {
        $contact = [
            'email' => SettingsHelper::get('contact_email', 'info@rehagazetesi.com'),
            'phone' => SettingsHelper::get('contact_phone', ''),
            'address' => SettingsHelper::get('contact_address', ''),
        ];
        return view('frontend.pages.contact', compact('contact'));
    }
}
