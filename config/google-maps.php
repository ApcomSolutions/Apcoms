<?php
// File: config/google-maps.php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Maps API Key
    |--------------------------------------------------------------------------
    |
    | API Key untuk Google Maps. Direkomendasikan untuk menyimpan API key
    | di .env file dan mereferensikan di sini.
    |
    */
    'api_key' => env('GOOGLE_MAPS_API_KEY', 'AIzaSyAwu4CGUgxRjUN4pahOIpTsKmKw35gWgN8'),

    /*
    |--------------------------------------------------------------------------
    | Default Location
    |--------------------------------------------------------------------------
    |
    | Lokasi default yang akan digunakan ketika tidak ada koordinat yang diberikan.
    |
    */
    'default_lat' => env('GOOGLE_MAPS_DEFAULT_LAT', -6.938139),
    'default_lng' => env('GOOGLE_MAPS_DEFAULT_LNG', 107.666861),
    'default_zoom' => env('GOOGLE_MAPS_DEFAULT_ZOOM', 15),

    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    |
    | Informasi perusahaan untuk ditampilkan pada marker dan info window.
    |
    */
    'company_name' => env('COMPANY_NAME', 'APCOM Solutions'),
    'company_address' => env('COMPANY_ADDRESS', 'Sanggar Kencana Utama No. 1C Sanggar Hurip Estate, Jatisari, Buahbatu, Soekarno Hatta, Kota Bandung, Jawa Barat'),
];
