<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function index()
    {
        return CartItemResource::collection(CartItem::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer'],
        ]);

        return new CartItemResource(CartItem::create($data));
    }

    public function show(CartItem $cartItem)
    {
        return new CartItemResource($cartItem);
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users'],
            'product_id' => ['required', 'exists:products'],
            'quantity' => ['required', 'integer'],
        ]);

        $cartItem->update($data);

        return new CartItemResource($cartItem);
    }

    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();

        return response()->json();
    }
}
