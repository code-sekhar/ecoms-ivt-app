<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserProfile;
use Exception;
class UserProfileController extends Controller
{
    //only get user profile
    public function getProfile(Request $request) {

        try {
            // 🔹 Ensure user is authenticated
            if (!Auth::check()) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            $user = Auth::user();
            $profile = UserProfile::where('user_id', $user->id)->first();
            if (!$profile) {
                return response()->json(['message' => 'Profile not found'], 404);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'User profile fetched successfully',
                'profile' => $profile,
                'image_url' => $profile->image ? asset($profile->image) : null
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Add Profile (Only for Logged-in User)
    public function addProfile(Request $request) {
        try{
            $request->validate([
                'name' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            $userId = Auth::id();
             // Check if profile already exists
             if (UserProfile::where('user_id', $userId)->exists()) {
                return response()->json(['message' => 'Profile already exists'], 400);
            }
            //Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('public/profile_images', $imageName);
                $imagePath = str_replace('public/', 'storage/', $imagePath); // Convert path for URL
            }
            $profile = UserProfile::create([
               'user_id' => $userId,
               'name' => $request->name,
               'phone' => $request->phone,
               'address' => $request->address,
               'image' => $imagePath
            ]);

            return response()->json([
                'message' => 'Profile added successfully',
                'data' => $profile,
                'image_url' => asset($imagePath)
            ]);
        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //Update Profile
    public function updateProfile(Request $request) {
        try {
            $request->validate([
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:15',
                'address' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $userId = Auth::id();
            $profile = UserProfile::where('user_id', $userId)->first();

            if (!$profile) {
                return response()->json(['message' => 'Profile not found'], 404);
            }

            // Handle Image Upload
            if ($request->hasFile('image')) {
                // Delete Old Image
                if ($profile->image && file_exists(public_path($profile->image))) {
                    unlink(public_path($profile->image));
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('public/profile_images', $imageName);
                $profile->image = str_replace('public/', 'storage/', $imagePath);
            }

            // Update Profile Data
            $profile->update([
                'name' => $request->name ?? $profile->name,
                'phone' => $request->phone ?? $profile->phone,
                'address' => $request->address ?? $profile->address,
            ]);

            return response()->json([
                'status' => 'success',
                'profile' => $profile,
                'image_url' => $profile->image ? asset($profile->image) : null
            ], 200);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
