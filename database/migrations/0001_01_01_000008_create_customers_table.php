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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->nullable();
            $table->foreignUuid('guest_id')->nullable();
            $table->integer('total_orders')->default(0);
            $table->integer('total_spent')->default(0);
            $table->integer('total_refunds')->default(0);
            $table->integer('monthly_orders_count')->default(0);
            $table->timestamp('last_order_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->unique('guest_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
