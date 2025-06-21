<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class);
            $table->foreignIdFor(Product::class);
            $table->foreignIdFor(User::class, 'seller_id');
            $table->decimal('price');
            $table->string('product_title');
            $table->string('download_url');
            $table->string('download_count');
            $table->string('max_downloads');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
