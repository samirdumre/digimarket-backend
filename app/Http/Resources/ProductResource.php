<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'download_count' => $this->download_count,
            'quantity' => $this->quantity,
            'thumbnail' => $this->thumbnail,
            'images' => $this->images,
            'category' => new Category($this)
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
