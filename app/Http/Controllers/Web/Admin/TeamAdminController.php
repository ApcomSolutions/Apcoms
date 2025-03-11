<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;

class TeamAdminController extends Controller
{
    public function index()
    {
        return view('Admin.TeamCrud');
    }
}
