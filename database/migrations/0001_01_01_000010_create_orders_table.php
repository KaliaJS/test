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
            $table->uuid('id')->primary();
            $table->string('slug');
            $table->foreignUuid('user_id')->nullable();
            $table->foreignUuid('guest_id')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->integer('total_amount');
            $table->integer('tip_amount')->nullable();
            $table->integer('refunded_amount')->nullable();
            $table->integer('total_manufacturing_time');
            $table->string('payment_error_code')->nullable();
            $table->string('payment_collected_code')->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index('slug');
            $table->index('user_id');
            $table->index('guest_id');
            $table->index('created_at');
        });

        Schema::create('order_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->integer('quantity');
            $table->boolean('is_done')->default(false);
            $table->integer('unit_price');
            $table->integer('total_price');
            $table->timestamps();

            $table->index(['order_id', 'is_done']);
            $table->index('product_id');
        });

        Schema::create('order_product_modifications', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_product_id')->constrained('order_products')->cascadeOnDelete();
            $table->foreignUuid('ingredient_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ingredient_name');
            $table->enum('action', ['remove', 'extra']);
            $table->integer('quantity')->default(0);
            $table->integer('supplement_price')->default(0);
            $table->timestamps();

            $table->index('order_product_id');
            $table->index(['order_product_id', 'action']);
        });

        Schema::create('order_makers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            
            $table->index('user_id');
        });

        Schema::create('order_maker_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_maker_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('order_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['order_id']);
            $table->index('order_maker_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_maker_orders');
        Schema::dropIfExists('order_makers');
        Schema::dropIfExists('order_product_modifications');
        Schema::dropIfExists('order_products');
        Schema::dropIfExists('orders');
    }
};
