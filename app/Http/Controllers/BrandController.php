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
}
