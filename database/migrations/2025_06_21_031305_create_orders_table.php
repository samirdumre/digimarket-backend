<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'buyer_id');
            $table->uuid('order_number');
            $table->decimal('total_amount');
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'refunded']);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded']);
            $table->string('payment_method');
            $table->string('billing_email');
            $table->string('billing_name');
            $table->string('billing_address');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
