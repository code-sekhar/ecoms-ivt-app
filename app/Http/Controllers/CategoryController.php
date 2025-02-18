<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\category;

class CategoryController extends Controller
{
    /*
    *function:add Category
    *Decription:This function is used to add category
    */
    public function addCategory(Request $request) {
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
                $imagePath = $image->storeAs('public/category_images', $imageName);
                $imagePath = str_replace('public/', 'storage/', $imagePath); // Convert path for URL
            }
            $category = category::create([
                'name' => $request->name,
                'image' => $imagePath
            ]);
            return response()->json([
                'message' => 'Category created successfully',
                'category' => $category,
                'image_url' => $category->image ? asset($category->image) : null
            ]);

        }catch(Exception $e){
            return response()->json([
                'message'=> $e->getMessage(),
            ],500);
        }
    }
    /*
    *function:Single Category
    *Decription:This function is used to get single category
    */
    public function singleCategory($id) {
        try{
            $category = category::findOrFail($id);
            return response()->json([
                'message' => 'Category fetched successfully',
                'category' => $category,
                'image_url' => $category->image ? asset($category->image) : null
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /*
    *function:All Category
    *Decription:This function is used to get all category
    */
    public function allCategory() {
        try{
            $categories = category::all();
            if ($categories->isEmpty()) {
                return response()->json(['message' => 'Categories not found'], 404);
            }
            return response()->json([
                'message' => 'Categories fetched successfully',
                'categories' => $categories
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /*
    *function:Update Category
    *Decription:This function is used to update category
    */
    public function updateCategory(Request $request, $id) {
        try{
            $request->validate([
                'name' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            $category = category::findOrFail($id);
            //Handele image upload
            $imagePath = $category->image;
            if ($request->hasFile('image')) {
                // Delete Old Image
                if ($category->image && file_exists(public_path($category->image))) {
                    unlink(public_path($category->image));
                }
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('public/category_images', $imageName);
                $imagePath = str_replace('public/', 'storage/', $imagePath); // Convert path for URL
            }
            $category->update([
                'name' => $request->name,
                'image' => $imagePath
            ]);
            return response()->json([
                'message' => 'Category updated successfully',
                'category' => $category,
                'image_url' => $category->image ? asset($category->image) : null
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /*
    *function :Delete Category
    *Description:This function is used to delete category
    */
    public function deleteCategory($id) {
        try{
            $category = category::findOrFail($id);
            // Delete Old Image
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }
            $category->delete();
            return response()->json([
                'message' => 'Category deleted successfully'
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
