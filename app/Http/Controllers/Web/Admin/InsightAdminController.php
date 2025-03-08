<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;

class InsightAdminController extends Controller
{
    public function index()
    {
        return view('Admin.InsightCrud'); // Load view admin
    }
}
