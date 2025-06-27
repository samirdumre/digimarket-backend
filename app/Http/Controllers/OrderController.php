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

        return new OrderResource(Order::create($data));
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
