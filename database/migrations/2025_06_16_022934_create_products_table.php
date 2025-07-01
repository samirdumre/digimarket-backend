<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->text('title');
            $table->text('short_description');
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->integer('quantity');
            $table->text('thumbnail');
            $table->json('images');
            $table->text('file_url');
            $table->text('file_name');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'inactive']);
            $table->integer('download_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
