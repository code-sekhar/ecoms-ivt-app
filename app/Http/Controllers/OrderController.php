<?php

namespace App\Http\Controllers;

use App\Models\orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class OrderController extends Controller
{
    public function createOrder(Request $request) {
        // Implement order creation logic here
        $request->validate([
           'products' => 'required|array',
           'product.*.product_id' => 'required|exists:products,id',
           'products.*.quantity' => 'required|integer|min:1',
        ]);
        $userId = Auth::id();
        DB::beginTransaction();
        try {
            $totalPrice = 0;
            $orderNumber = 'ORD-' . strtoupper(uniqid());
            $order = orders::create([
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'total_price' => 0,
                'status' => 'pending',
                'payment_method' => 'unpaid',
            ]);

            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['product_id']);
                $price = $product->price * $item['quantity'];
                $totalPrice += $price;
                $order->products()->attach($product->id,
                ['quantity' => $item['quantity'],
                'price' => $price,
            ]);
            }

            $order->update(['total_price' => $totalPrice]);
            DB::commit();
            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load('products'),
            ], 201);
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
