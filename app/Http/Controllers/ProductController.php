<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends BaseController
{
    public function index()
    {
        $products = Product::with('category')->get();
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
            'category_id' => ['required', 'exists:categories,id']
        ]);

        if($validator->fails()){
            return $this->sendError('Validation error', $validator->errors());
        }

        $dataToCreate = array_merge([
            'download_count' => 0,
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
        $data = $request->validate([
            'title' => ['sometimes','required'],
            'description' => ['sometimes','required'],
            'short_description' => ['sometimes','required'],
            'price' => ['sometimes','required', 'numeric'],
            'status' => ['sometimes', 'required', Rule::in(['draft', 'pending', 'approved', 'rejected', 'inactive'])],
            'thumbnail' => ['sometimes','required', 'url'],
            'quantity' => ['sometimes','numeric'],
            'images' => ['sometimes','required', 'array'],
            'images.*' => ['sometimes','string', 'url'], // Validates each item in the array is an url
            'category_id' => ['sometimes','required', 'exists:categories,id']
        ]);

        $product->update($data);

        return $this->sendResponse(new ProductResource($product), 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return $this->sendResponse([], 'Product deleted successfully');
    }
}
