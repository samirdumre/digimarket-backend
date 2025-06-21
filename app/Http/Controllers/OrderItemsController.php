<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderItemsResource;
use App\Models\OrderItems;
use Illuminate\Http\Request;

class OrderItemsController extends Controller
{
    public function index()
    {
        return OrderItemsResource::collection(OrderItems::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'exists:orders'],
            'product_id' => ['required', 'exists:products'],
            'seller_id' => ['required', 'exists:users'],
            'price' => ['required', 'numeric'],
            'product_title' => ['required'],
            'download_url' => ['required'],
            'download_count' => ['required'],
            'max_downloads' => ['required'],
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
            'order_id' => ['required', 'exists:orders'],
            'product_id' => ['required', 'exists:products'],
            'seller_id' => ['required', 'exists:users'],
            'price' => ['required', 'numeric'],
            'product_title' => ['required'],
            'download_url' => ['required'],
            'download_count' => ['required'],
            'max_downloads' => ['required'],
        ]);

        $orderItems->update($data);

        return new OrderItemsResource($orderItems);
    }

    public function destroy(OrderItems $orderItems)
    {
        $orderItems->delete();

        return response()->json();
    }
}
