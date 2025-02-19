<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Product;
use Exception;
class ProductController extends Controller
{
    //new product store

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'required',
                'image.*' => 'image|mimes:jpeg,png,jpg,gif|max:5048', // প্রতিটি ইমেজ যাচাই
                'description' => 'nullable|string',
                'sub_description' => 'required|string',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
                'category_id' => 'required|integer',
                'brand_id' => 'required|integer'
            ]);

            $userId = Auth::id();
            $imagePaths = [];

            // ✅ একাধিক ইমেজ আপলোড প্রসেস
            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $image) {
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('public/product_images', $imageName);
                    $imagePath = str_replace('public/', 'storage/', $imagePath);
                    $imagePaths[] = $imagePath;
                }
            }

            $product = Product::create([
                'user_id' => $userId,
                'name' => $request->name,
                'image' => json_encode($imagePaths), // ✅ ইমেজ JSON আকারে সংরক্ষণ
                'description' => $request->description,
                'sub_description' => $request->sub_description,
                'price' => $request->price,
                'stock' => $request->stock,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => '🎉 প্রোডাক্ট সফলভাবে তৈরি হয়েছে!',
                'product' => $product
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '❌ কিছু সমস্যা হয়েছে!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //get all products
    public function getAllProducts() {
        try{
            $products = Product::all();
            if ($products->isEmpty()) {
                return response()->json(['message' => 'Products not found'], 404);
            }
            return response()->json([
                'message' => 'Products fetched successfully',
                'products' => $products
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //get single product
    public function getSingleProduct($id) {
        try{
            $product = Product::find($id);
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }
            $product->image = json_decode($product->image);
            return response()->json([
                'message' => 'Product fetched successfully',
                'product' => $product,
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //update product with image
    public function updateProduct(Request $request, $id) {
        try{
           $request->validate([
              'name' => 'required|string|max:255',
              'image' => 'sometimes|array',
              'image.*' => 'image|mimes:jpeg,png,jpg,gif|max:5048', // প্রতিটি ইমেজ যাচাই
              'description' => 'nullable|string',
              'sub_description' => 'required|string',
              'price' => 'required|numeric',
              'stock' => 'required|integer',
              'category_id' => 'required|integer',
              'brand_id' => 'required|integer'
           ]);

           $product = Product::find($id);
           $userId = Auth::id();
           $imagePaths = json_decode($product->image, true)??[];

           // ✅ একাধিক ইমেজ আপলোড প্রসেস
           if ($request->hasFile('image')) {
               foreach ($request->file('image') as $image) {
                   $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                   $imagePath = $image->storeAs('public/product_images', $imageName);
                   $imagePath = str_replace('public/', 'storage/', $imagePath);
                   $imagePaths[] = $imagePath;
               }
           }

           $product->user_id = $userId;
           $product->name = $request->name;
           $product->image = json_encode($imagePaths); // ✅ ইমেজ JSON আকারে সংরক্ষণ
           $product->description = $request->description;
           $product->sub_description = $request->sub_description;
           $product->price = $request->price;
           $product->stock = $request->stock;
           $product->category_id = $request->category_id;
           $product->brand_id = $request->brand_id;
           $product->save();

           return response()->json([
               'message' => 'Product updated successfully',
               'product' => $product
           ], 200);
        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteProduct($id) {
        try {
            $product = Product::find($id);
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }
            // Delete Old Images (assuming 'images' is a JSON array in DB)
            if ($product->image) {
                $images = json_decode($product->image, true); // Decode JSON to array
                foreach ($images as $image) {
                    $imagePath = public_path($image);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }

            $product->delete();

            return response()->json([
                'message' => 'Product and associated images deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
