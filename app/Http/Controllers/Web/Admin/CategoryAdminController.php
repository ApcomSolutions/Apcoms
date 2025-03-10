<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;

class CategoryAdminController extends Controller
{
    public function index()
    {
        return view('Admin.CategoryCrud'); // Load admin view for Category CRUD
    }
}
