<?php

namespace App\Http\Controllers\Web\Admin;

class NewsAdminController
{
    public function index()
    {
        return view('Admin.NewsCrud');
    }
}
