<?php

use RalphJSmit\Laravel\SEO\Models\SEO;

return [
    /**
     * Model SEO yang digunakan. Pengaturan ini dapat digunakan untuk mengganti model default.
     */
    'model' => SEO::class,

    /**
     * Nama situs yang akan digunakan dalam tag OpenGraph.
     */
    'site_name' => 'ApCom Solutions - Membangun Reputasi Menciptakan Solusi',

    /**
     * Path menuju sitemap website Anda. Pastikan diawali dengan slash (/).
     */
    'sitemap' => '/sitemap.xml',

    /**
     * Aktifkan link canonical untuk menghindari konten duplikat.
     */
    'canonical_link' => true,

    'robots' => [
        /**
         * Nilai default untuk tag robots.
         */
        'default' => 'max-snippet:-1,max-image-preview:large,max-video-preview:-1',

        /**
         * Paksa nilai default robots (berguna jika ingin semua halaman noindex).
         */
        'force_default' => false,
    ],

    /**
     * Path menuju favicon website Anda (relatif terhadap folder public).
     */
    'favicon' => 'favicon.ico',

    'title' => [
        /**
         * Aktifkan fitur untuk otomatis mengambil judul dari URL jika tidak ada judul lain.
         */
        'infer_title_from_url' => true,

        /**
         * Suffix yang akan ditambahkan setelah judul pada setiap halaman.
         */
        'suffix' => ' | ApCom Solutions',

        /**
         * Judul khusus untuk halaman beranda.
         */
        'homepage_title' => 'ApCom Solutions - Membangun Reputasi Menciptakan Solusi',
    ],

    'description' => [
        /**
         * Deskripsi default yang akan digunakan jika tidak ada deskripsi yang ditetapkan.
         */
        'fallback' => 'ApCom Solutions menyediakan layanan komunikasi dan PR strategis untuk membantu bisnis Anda membangun reputasi yang kuat dan menciptakan solusi komunikasi yang efektif.',
    ],

    'image' => [
        'fallback' => 'images/logo.png',
    ],

    'author' => [
        /**
         * Penulis default yang akan digunakan jika tidak ada penulis yang ditetapkan.
         */
        'fallback' => 'ApCom Solutions',
    ],

    'twitter' => [
        /**
         * Username Twitter tanpa tanda @. Kosongkan jika tidak memiliki Twitter.
         */
        '@username' => null,
    ],

    /**
     * Custom metadata yang ingin ditambahkan pada semua halaman.
     * Anda bisa menambahkan akun sosial media di sini.
     * Catatan: Ini adalah ekstensi kustom yang mungkin perlu Anda tambahkan ke fitur package.
     */
    'social_media' => [
        'instagram' => 'https://www.instagram.com/apcomsolution',
    ],
];
