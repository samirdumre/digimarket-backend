<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return OrderResource::collection(Order::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'buyer_id' => ['required', 'exists:users'],
            'order_number' => ['required'],
            'total_amount' => ['required', 'numeric'],
            'status' => ['required'],
            'payment_status' => ['required'],
            'payment_method' => ['required'],
            'billing_email' => ['required', 'email', 'max:254'],
            'billing_name' => ['required'],
            'billing_address' => ['required'],
        ]);

        return new OrderResource(Order::create($data));
    }

    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'buyer_id' => ['required', 'exists:users'],
            'order_number' => ['required'],
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
