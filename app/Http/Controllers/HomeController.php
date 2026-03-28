<?php

namespace App\Http\Controllers;

use App\Services\NewsService;

class HomeController extends Controller
{
    public function __construct(
        protected NewsService $newsService
    ) {}

    /**
     * Ana sayfa
     */
    public function index()
    {
        $data = $this->newsService->getHomePageData();

        return view('frontend.home', $data);
    }
}
