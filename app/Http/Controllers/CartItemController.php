<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartItemController extends Controller
{
    public function index()
    {
        return CartItemResource::collection(CartItem::all());
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $quantity = 1;

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        // Check if the product already exists in the user's cart
        $existingCartItem = $user->cartItems()->where('product_id', $data['product_id'])->first();

        if($existingCartItem){
            // Increase product quantity if it exists
            $existingCartItem->increment('quantity');
            return new CartItemResource($existingCartItem);
        }

        $cart_item = array_merge($data, [
            'user_id' => $user_id,
            'quantity' => $quantity
        ]);

        return new CartItemResource(CartItem::create($cart_item));
    }

    public function show(CartItem $cartItem)
    {
        return new CartItemResource($cartItem);
    }

//    public function update(Request $request, CartItem $cartItem)
//    {
//        $data = $request->validate([
//            'user_id' => ['required', 'exists:users'],
//            'product_id' => ['required', 'exists:products'],
//            'quantity' => ['required', 'integer'],
//        ]);
//
//        $cartItem->update($data);
//
//        return new CartItemResource($cartItem);
//    }

    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();

        return response()->json();
    }
}
