<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 40)->unique();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 50)->nullable();
            $table->text('customer_address')->nullable();
            // Snapshot of the products at order time: [{product_id, name, barcode, image, qty}]
            $table->json('items');
            $table->unsignedInteger('total_pcs');
            $table->string('locale', 5)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
