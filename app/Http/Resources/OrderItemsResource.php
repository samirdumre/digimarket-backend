<?php

namespace App\Http\Resources;

use App\Models\OrderItems;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin OrderItems */
class OrderItemsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'product_title' => $this->product_title,
            'download_url' => $this->download_url,
            'download_count' => $this->download_count,
            'max_downloads' => $this->max_downloads,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'seller_id' => $this->seller_id,
        ];
    }
}
