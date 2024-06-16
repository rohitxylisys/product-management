<?php

// app/Http/Controllers/ProductController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('categories')->get();
        return view('product_list', ['products' => $products]);
    }
    public function getUserList()
    {
        // Fetch all users with their roles and permissions
        $users = User::with(['roles', 'roles.permissions'])->get();

        // Transform users
        $transformedUsers = $users->map(function ($user) {
            // Extract the first role's name (assuming the user has only one role)
            $role = $user->roles->first(); // Get the first role object

            // Extract permission names for the first role
            $permissions = $role ? $role->permissions->pluck('name')->toArray() : [];

            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $role ? $role->name : null, // Get role name if available
                'permissions' => $permissions,
            ];
        });

        return view('user_list', ['users' => $transformedUsers]);
    }

    public function show($slug)
    {
        $product = Product::with('categories')->where('slug', $slug)->first();

        if (is_null($product)) {
            return redirect()->route('products.index')->with('error', 'Product not found');
        }

        $categories = Category::all(); // Fetch all categories

        // Retrieve selected category IDs for the product
        $selectedCategories = $product->categories->pluck('id')->toArray();

        return view('product_details', [
            'product' => $product,
            'mode' => 'view',
            'categories' => $categories,
            'selectedCategories' => $selectedCategories
        ]);
    }
    public function create()
    {
        $product = new Product();
        $categories = Category::all(); // Fetch all categories

        return view('product_details', [
            'product' => $product,
            'mode' => 'create',
            'categories' => $categories,
            'selectedCategories' => [] // No selected categories for a new product
        ]);
    }


    public function edit($slug)
    {
        // Fetch the product by slug along with its associated categories
        $product = Product::where('slug', $slug)->first();
    
        // Check if the product exists
        if (is_null($product)) {
            return redirect()->route('products.index')->with('error', 'Product not found');
        }
    
        // Fetch all categories from the database
        $categories = Category::all();
    
        // Get the IDs of categories that are currently associated with the product
        $selectedCategories = $product->categories->pluck('id')->toArray();
    
        // Return the view with the product, categories, and selectedCategories
        return view('product_details', [
            'product' => $product,
            'mode' => 'edit',
            'categories' => $categories,
            'selectedCategories' => $selectedCategories
        ]);
    }
    public function store(Request $request, $slug = null)
    {
        // Validate the request based on whether it's for creating or updating
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'featured_image' => $slug ? 'nullable|image|max:2048' : 'required|image|max:2048', // For update, featured_image is optional
            'description' => 'required|string',
            'status' => 'required|in:Active,Inactive',
            'category_id' => 'required|array', // ensure category_id is an array
            'category_id.*' => 'exists:categories,id', // check if each category_id exists in categories table
            'gallery.*' => 'nullable|image|max:2048',
        ]);
    
        // Handle featured image and gallery
        $featuredImage = null;
        if ($request->hasFile('featured_image')) {
            $featuredImage = $request->file('featured_image')->store('public/images');
        }
    
        $galleryImages = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $galleryImages[] = $image->store('public/images');
            }
        }
    
        // Create or update product based on $slug presence
        if ($slug) {
            // Update existing product
            $product = Product::where('slug', $slug)->firstOrFail();
        } else {
            // Create new product
            $product = new Product();
        }
    
        // Assign values to product model
        $product->title = $validatedData['title'];
        $product->slug = Str::slug($validatedData['title']);
        if ($featuredImage) {
            $product->featured_image = str_replace('public/', '', $featuredImage);
        }
        $product->gallery = json_encode($galleryImages);
        $product->description = $validatedData['description'];
        $product->status = $validatedData['status'];
        $product->save();
    
        // Sync categories with the product
        $product->categories()->sync($validatedData['category_id']);
    
        // Redirect back with success message
        return redirect()->route('products.index')->with('success', $slug ? 'Product updated successfully.' : 'Product added successfully.');
    }
    public function destroy($slug)
    {
        $product = Product::where('slug', $slug)->first();
    
        if (is_null($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    
        // Detach categories associated with the product
        $product->categories()->detach();
    
        // Delete the product
        $product->delete();
    
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
