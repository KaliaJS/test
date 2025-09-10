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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('price');
            $table->integer('price_points')->nullable();
            $table->tinyInteger('type');
            $table->boolean('is_homemade');
            $table->tinyInteger('organic_type')->nullable();
            $table->integer('container_quantity')->nullable();
            $table->integer('container_quantity_format')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('manufacturing_time')->nullable();
            $table->integer('profit_margin')->nullable();
            $table->integer('stock')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('highlights', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        Schema::create('highlight_product', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('product_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('highlight_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('highlight_product');
        Schema::dropIfExists('products');
        Schema::dropIfExists('highlights');
    }
};
