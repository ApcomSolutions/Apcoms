<?php
// File: app/View/Components/GoogleMap.php

namespace App\View\Components;

use Illuminate\View\Component;

class GoogleMap extends Component
{
    public $id;
    public $lat;
    public $lng;
    public $zoom;
    public $title;
    public $address;
    public $height;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $id = null,
        $lat = -6.938139,
        $lng = 107.666861,
        $zoom = 15,
        $title = "APCOM Solutions",
        $address = "Sanggar Kencana Utama No. 1C Sanggar Hurip Estate, Jatisari, Buahbatu, Soekarno Hatta, Kota Bandung, Jawa Barat",
        $height = "16rem" // 256px / 64
    ) {
        $this->id = $id ?: 'map-' . uniqid();
        $this->lat = $lat;
        $this->lng = $lng;
        $this->zoom = $zoom;
        $this->title = $title;
        $this->address = $address;
        $this->height = $height;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.google-map');
    }
}
