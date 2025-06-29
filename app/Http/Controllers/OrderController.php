<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        return OrderResource::collection(Order::all());
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'total_amount' => ['required', 'numeric'],
            'status' => ['required'],
            'payment_status' => ['required'],
            'payment_method' => ['required'],
            'billing_name' => ['required'],
            'billing_address' => ['required'],
        ]);

        $data['order_number'] = uuid_create();
        $data['buyer_id'] = $user->id;
        $data['billing_email'] = $user->email;

        // Create the order
        $order = Order::create($data);

        // Get the user's cart items
        $cartItems = $user->cartItem()->with('product')->get();

        // Create order items
        foreach ($cartItems as $cartItem){
            $order->orderItems()->create([
                'product_id' => $cartItem->product_id,
                'seller_id' => $cartItem->product->seller_id,
                'price' => $cartItem->product->price,
                'product_title' => $cartItem->product->title,
                'download_url' => $cartItem->product->file_url
            ]);
        }

        return new OrderResource($order->load('orderItems'));
    }

    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'buyer_id' => ['required', 'exists:users,id'],
            'total_amount' => ['required', 'numeric'],
            'status' => ['required'],
            'payment_status' => ['required'],
            'payment_method' => ['required'],
            'billing_email' => ['required', 'email', 'max:254'],
            'billing_name' => ['required'],
            'billing_address' => ['required'],
        ]);

        $order->update($data);

        return new OrderResource($order);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json();
    }
}
