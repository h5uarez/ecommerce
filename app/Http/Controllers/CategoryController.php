<?php

namespace App\Http\Controllers;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }
}
