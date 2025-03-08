<?php

namespace App\Http\Controllers\Web;
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Insight;

class InsightWebController extends Controller
{
    public function index()
    {
        $insights = Insight::all(); // Ambil semua data insights
        return view('insights.insights', compact('insights')); // Kirim ke Blade
    }

    public function show($id)
    {
        $insight = Insight::findOrFail($id);
        return view('insights.detail', compact('insight'));
    }

}
