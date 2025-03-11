<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use App\Models\Category;
use Illuminate\Http\Request;

class InsightWebController extends Controller
{
    public function index()
    {
        // Ambil insights dengan pagination
        $insights = Insight::orderBy('TanggalTerbit', 'desc')->paginate(12);
        return view('insights.index', compact('insights'));
    }
    
    public function show($slug)
    {
        $insight = Insight::where('slug', $slug)->firstOrFail();
        return view('insights.show', compact('insight'));
    }
    
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $insights = Insight::where('category_id', $category->id)
                          ->orderBy('TanggalTerbit', 'desc')
                          ->paginate(12);
        
        return view('insights.index', compact('insights', 'category'));
    }
    
    public function search(Request $request)
    {
        $query = $request->input('query');
        $insights = Insight::where('judul', 'like', "%{$query}%")
                          ->orWhere('isi', 'like', "%{$query}%")
                          ->orderBy('TanggalTerbit', 'desc')
                          ->paginate(12);
        
        return view('insights.index', compact('insights', 'query'));
    }
}