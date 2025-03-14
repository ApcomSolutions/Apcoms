<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class SEOServiceProvider extends ServiceProvider
{
    public function boot()
    {
        view()->composer('*', function ($view) {
            if (!isset($view->getData()['seoData'])) {
                $view->with('seoData', new SEOData(
                    title: 'ApCom Solutions - Membangun Reputasi Menciptakan Solusi',
                    description: 'ApCom Solutions menyediakan layanan komunikasi dan PR strategis untuk membantu bisnis Anda membangun reputasi yang kuat.',
                    url: url()->current()
                ));
            }
        });
    }
}
