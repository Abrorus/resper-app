<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name')->nullable();
            $table->unsignedInteger('guest_count')->nullable();
            $table->string('table_number')->nullable();
            $table->string('type')->default('offline');      // offline / third_party
            $table->string('platform')->nullable();          // GoFood, GrabFood, ShopeeFood
            $table->string('status')->default('pending');    // pending, processed, cancelled
            $table->unsignedInteger('total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
