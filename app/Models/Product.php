<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'title', 'description', 'short_description', 'price', 'status', 'quantity','download_count', 'thumbnail', 'images', 'category_id'
    ];

    protected $casts = [
        'images' => 'array', // Casts the JSON column to an array
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
