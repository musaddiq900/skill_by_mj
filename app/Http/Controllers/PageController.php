<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::published()->where('slug', $slug)->firstOrFail();

        // Increment view count
        $page->increment('view_count');

        return view('pages.show', compact('page'));
    }

    public function home()
    {
        $page = Page::published()->where('slug', 'home')->first();

        if (!$page) {
            abort(404);
        }

        return view('pages.home', compact('page'));
    }
}