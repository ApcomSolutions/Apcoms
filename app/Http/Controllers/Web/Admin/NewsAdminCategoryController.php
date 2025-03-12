<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewsAdminCategoryController extends Controller
{
    /**
     * Display the news categories management page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Generate a cache buster to ensure the browser loads fresh assets
        $cacheBuster = time();

        return view('Admin.NewsCategoryCrud', [
            'cacheBuster' => $cacheBuster
        ]);
    }
}
