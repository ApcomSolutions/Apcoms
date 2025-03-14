<?php

namespace App\Http\Controllers;

use RalphJSmit\Laravel\SEO\Support\SEOData;

class HomeController
{
    public function index() {
        // SEO untuk halaman beranda
        $seoData = new SEOData(
            title: 'ApCom Solutions - Membangun Reputasi Menciptakan Solusi',
            description: 'ApCom Solutions adalah konsultan komunikasi dan PR yang membantu Anda membangun reputasi yang kuat.',
            url: route('home')
        );

        return view('home', ['seoData' => $seoData]);
    }
}
