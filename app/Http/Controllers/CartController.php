<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\carts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CartController extends Controller
{
    //add Cart
    public function addCart(Request $request){
        try{
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);
            $authUser = Auth::user();
            $cartItem = carts::where('user_id', $authUser->id)->where('product_id', $request->product_id)->first();
            if($cartItem){
                $cartItem->quantity += $request->quantity;
                $cartItem->save();
            }else{
                $cart = carts::create([
                    'user_id' => $authUser->id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                ]);
            }

            return response()->json([
                'message' => 'Cart added successfully',
                'cart' => $cart,
            ], 201);
        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function viewCart(Request $request){
        try{
            $authUser = Auth::user();
            $carts = carts::where('user_id', $authUser->id)->get();
            return response()->json([
                'message' => 'Cart fetched successfully',
                'carts' => $carts,
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
