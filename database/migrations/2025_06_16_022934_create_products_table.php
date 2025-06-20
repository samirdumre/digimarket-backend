<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id');
            $table->string('title');
            $table->string('short_description');
            $table->string('description');
            $table->decimal('price');
            $table->integer('quantity');
            $table->string('thumbnail');
            $table->json('images');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'inactive']);
            $table->integer('download_count');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
