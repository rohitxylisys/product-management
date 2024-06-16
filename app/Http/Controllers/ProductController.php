<?php

// app/Http/Controllers/ProductController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
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
        return view('product_details', ['product' => $product, 'mode' => 'view']);
    }
    public function create()
    {
        // Typically, you might initialize a new Product model instance
        $product = new Product();

        return view('product_details', [
            'product' => $product,
            'mode' => 'create'
        ]);
    }

    public function edit($slug)
    {
        $product = Product::with('categories')->where('slug', $slug)->first();
        if (is_null($product)) {
            return redirect()->route('products.index')->with('error', 'Product not found');
        }
        return view('product_details', ['product' => $product, 'mode' => 'edit']);
    }
    public function store(Request $request)
    {
        info("call");
        // Validate incoming request
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:Active,Inactive',
            'featured_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        // Use slug based on title (if slug is not directly provided in the form)
        info(Str::slug($validatedData['title']));
        $slug = Str::slug($validatedData['title']);
        // Check if a product with the given slug exists
        $product = Product::where('slug', $slug)->first();
        info($product);
    
        if (!$product) {
            // Product doesn't exist, create a new one
            $product = new Product();
            $product->slug = $slug;
        }
        info("call2");
    
        // Update or set fields
        $product->title = $validatedData['title'];
        $product->description = $validatedData['description'];
        $product->status = $validatedData['status'];
    
        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $featuredImage = $request->file('featured_image');
            $path = $featuredImage->store('public');
            $product->featured_image = str_replace('public/', '', $path);
        }
    
        // Handle gallery images
        if ($request->hasFile('gallery')) {
            $gallery = $request->file('gallery');
            $galleryPaths = [];
    
            foreach ($gallery as $image) {
                $path = $image->store('public');
                $galleryPaths[] = str_replace('public/', '', $path);
            }
    
            $product->gallery = json_encode($galleryPaths   );
        }

        if ($request->has('delete_gallery')) {
            $deleteGallery = $request->input('delete_gallery');
            $existingGallery = json_decode($product->gallery);
    
            // Remove selected images from storage and update gallery JSON
            foreach ($deleteGallery as $imageToDelete) {
                // Remove from storage
                $path = 'public/' . $imageToDelete;
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
    
                // Remove from JSON array
                $existingGallery = array_diff($existingGallery, [$imageToDelete]);
            }
    
            // Update product gallery
            $product->gallery = json_encode(array_values($existingGallery)); // re-index array
        }
    
        // Save the product
        $product->save();
    
        // Determine if this was a create or update action
        $action = $product->wasRecentlyCreated ? 'created' : 'updated';
    
        return redirect()->route('products.edit', ['slug' => $product->slug])
                         ->with('success', "Product $action successfully");
    }

    public function destroy($slug)
    {
        $product = Product::where('slug', $slug)->first();
        if (is_null($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }

}
