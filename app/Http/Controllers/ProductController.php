<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends BaseController
{
    public function index()
    {
        $products = Product::with('category')->orderBy('id', 'asc')->get();
        return $this->sendResponse(ProductResource::collection($products), 'Products retrived successfully');
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => ['required'],
            'description' => ['required'],
            'short_description' => ['required'],
            'price' => ['required', 'numeric'],
            'status' => ['required', Rule::in(['draft', 'pending', 'approved', 'rejected', 'inactive'])],
            'thumbnail' => ['required', 'url'],
            'quantity' => ['numeric'],
            'images' => ['required', 'array'], // Validates the images is an array
            'images.*' => ['string', 'url'], // Validates each item in the array is a url
            'category_id' => ['required', 'exists:categories,id'],
            'file_url' => ['sometimes','required'],
            'file_name' => ['sometimes','required']
        ]);

        if($validator->fails()){
            return $this->sendError('Validation error', $validator->errors());
        }

        $user = Auth::user();

        $dataToCreate = array_merge([
            'download_count' => 0,
            'seller_id' => $user->id
        ], $validator->validated());

        $product = Product::create($dataToCreate);

        return $this->sendResponse(new ProductResource($product), 'Product created successfully');
    }

    public function show(Product $product)
    {
        return $this->sendResponse(new ProductResource($product), 'Product retrived successfully');
    }

    public function update(Request $request, Product $product)
    {
        $user = Auth::user();
        if($user->id !== $product->seller_id){
            return $this->sendError("You can only modify your products");
        }

        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'short_description' => ['sometimes', 'required', 'string', 'max:500'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'status' => ['sometimes', 'required', Rule::in(['draft', 'pending', 'approved', 'rejected', 'inactive'])],
            'thumbnail' => ['sometimes', 'required', 'url'],
            'quantity' => ['sometimes', 'numeric', 'min:0'],
            'images' => ['sometimes', 'required', 'array'],
            'images.*' => ['string', 'url'],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'file_url' => ['sometimes', 'nullable', 'url'],
            'file_name' => ['sometimes', 'nullable', 'string']
        ]);

        $product->update($data);

        return $this->sendResponse(new ProductResource($product), 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return $this->sendResponse([], 'Product deleted successfully');
    }

    public function userProducts()
    {
        $user = Auth::user();
        $products= $user->products()->with('category')->orderBy('id', 'asc')->get();
        return response()->json(ProductResource::collection($products));
    }

    public function getProductsFromCart()
    {
        $user = Auth::user();
        $cartItems = $user->cartItem()->with('product.category')->get();

        $products = $cartItems->map(function ($cartItem){
            return [
                'cart_item_id' => $cartItem->id,
                'quantity' => $cartItem->quantity,
                'product' => new ProductResource($cartItem->product)
            ];
        });
        return $this->sendResponse($products, 'Cart products retrieved successfully');
    }
}
