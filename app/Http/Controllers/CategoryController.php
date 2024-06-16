<?php
// app/Http/Controllers/CategoryController.php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::get();
        return view('categories_list', compact('categories'));
    }

    public function add()
    {
        return view('categories_create');
    }

    public function edit($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        return view('categories_edit', compact('category'));
    }

    public function getProductsByCategory(Request $request)
{
    $categoryId = $request->category_id;

    // Fetch products by category ID
    $products = Product::whereHas('categories', function ($query) use ($categoryId) {
        $query->where('categories.id', $categoryId);
    })->get();

    // Return JSON response with products data
    return response()->json($products);
}
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);

        $category = Category::create($data);
        return redirect()->route('categories.index')->with('success', 'category added successfully');
    }

    public function show($id)
    {
        $category = Category::with('product')->find($id);
        if (is_null($category)) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($category);
    }

    public function update(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:Active,Inactive',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
    
        $category = Category::where('slug', $slug)->first();
    
        if (is_null($category)) {
            return response()->json(['message' => 'Category not found'], 404);
        }
    
        // Update the title and generate slug if provided
        if ($request->has('title')) {
            $category->title = $request->title;
            $category->slug = Str::slug($request->title);
        }
    
        // Update the status if provided
        if ($request->has('status')) {
            $category->status = $request->status;
        }
    
        $category->save();
        return redirect()->route('categories.index')->with('success', 'category updated successfully');

    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (is_null($category)) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'category deleted successfully');
    }
}

