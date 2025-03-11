<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;

class GalleryAdminController extends Controller
{
    public function index()
    {
        return view('Admin.GalleryCrud');
    }
}
