<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index($slug)
    {
        $page = Page::where('slug', $slug)
            ->first();
        
        if ($page) {
            return view('site.page', compact('page'));
        } else {
            abort(404);
        }

    }
}
