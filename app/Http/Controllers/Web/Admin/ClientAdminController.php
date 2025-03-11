<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;

class ClientAdminController extends Controller
{
    public function index()
    {
        return view('Admin.ClientCrud');
    }
}
