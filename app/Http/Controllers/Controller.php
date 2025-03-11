<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function index()
    {
        $insights = YourModel::paginate(12); // This limits to 12 items per page
        return view('insights.index', compact('insights'));
    }
}
