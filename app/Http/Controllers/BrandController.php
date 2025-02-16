<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Brand;
class BrandController extends Controller
{
    //add Brand
    public function addBrand(Request $request){
        try{
            $request->validate([
                'name' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            //Handele image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('public/brand_images', $imageName);
                $imagePath = str_replace('public/', 'storage/', $imagePath); // Convert path for URL
            }
            $brand = Brand::create([
                'name' => $request->name,
                'image' => $imagePath
            ]);
            return response()->json([
                'message' => 'Brand added successfully',
                'brand' => $brand,
                'image_url' => $brand->image ? asset($brand->image) : null
            ], 201);
        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //update Brand
    public function updateBrand(Request $request, $id) {
        try{
            $request->validate([
                'name' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            $brand = Brand::findOrFail($id);
            //Handle image upload
            $imagePath = $brand->image;
            if ($request->hasFile('image')) {
                // Delete Old Image
                if ($brand->image && file_exists(public_path($brand->image))) {
                    unlink(public_path($brand->image));
                }
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('public/brand_images', $imageName);
                $imagePath = str_replace('public/', 'storage/', $imagePath); // Convert path for URL
            }
            $brand->update([
                'name' => $request->name,
                'image' => $imagePath
            ]);
            return response()->json([
                'message' => 'Brand updated successfully',
                'brand' => $brand,
                'image_url' => $brand->image ? asset($brand->image) : null
            ], 200);

        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //delete Brand
    public function deleteBrand($id) {
        try{
            $brand = Brand::findOrFail($id);


            // Delete Old Image
            if ($brand->image && file_exists(public_path($brand->image))) {
                unlink(public_path($brand->image));
            }

            $brand->delete();
            return response()->json([
                'message' => 'Brand deleted successfully'
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //get all Brands
    public function getAllBrands() {
        try{
            $brands = Brand::all();
            if ($brands->isEmpty()) {
                return response()->json(['message' => 'Brands not found'], 404);
            }
            return response()->json([
                'message' => 'Brands fetched successfully',
                'brands' => $brands
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //get single Brand
    public function getBrand($id) {
        try{
            $brand = Brand::findOrFail($id);
            return response()->json([
                'message' => 'Brand fetched successfully',
                'brand' => $brand,
                'image_url' => $brand->image ? asset($brand->image) : null
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
