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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('type')->nullable();
            $table->integer('quantity')->nullable();
            $table->tinyInteger('organic_type')->nullable();
            $table->boolean('is_swiss');
            $table->integer('supplement_price')->nullable();
            $table->integer('max_supplement')->nullable();
            $table->boolean('is_removable')->default(false);
            $table->timestamp('prepared_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('ingredient_product', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('ingredient_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained()->onDelete('cascade');
            $table->boolean('is_showed')->default(true);
            $table->integer('quantity')->nullable();
            $table->integer('quantity_format')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('category_ingredient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('ingredient_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_ingredient');
        Schema::dropIfExists('ingredient_product');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('ingredients');
    }
};
