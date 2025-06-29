<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderItemsResource;
use App\Models\OrderItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderItemsController extends Controller
{
    public function index()
    {
        return OrderItemsResource::collection(OrderItems::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'product_id' => ['required', 'exists:products,id'],
            'seller_id' => ['required', 'exists:users,id'],
            'price' => ['required', 'numeric'],
            'download_url' => ['required'],
            'product_title' => ['required'],
        ]);

        return new OrderItemsResource(OrderItems::create($data));
    }

    public function show(OrderItems $orderItems)
    {
        return new OrderItemsResource($orderItems);
    }

    public function update(Request $request, OrderItems $orderItems)
    {
        $data = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'product_id' => ['required', 'exists:products,id'],
            'seller_id' => ['required', 'exists:users,id'],
            'price' => ['required', 'numeric'],
            'product_title' => ['required'],
            'download_url' => ['required'],
        ]);

        $orderItems->update($data);

        return new OrderItemsResource($orderItems);
    }

    public function destroy(OrderItems $orderItems)
    {
        $orderItems->delete();

        return response()->json();
    }

    public function getPurchasedItems()
    {
        $user = Auth::user();
        $orderItems = $user->orderItems;
        return OrderItemsResource::collection($orderItems);
    }
}
