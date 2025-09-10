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
        Schema::create('schedule_places', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->decimal('coords_latitude', 10, 8)->nullable();
            $table->decimal('coords_longitude', 11, 8)->nullable();
            $table->timestamps();
        });

        Schema::create('schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('truck_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->boolean('is_active');
            $table->timestamps();
        });

        Schema::create('schedule_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->foreignUuid('schedule_place_id')->nullable()->constrained('schedule_places')->onDelete('cascade');
            $table->tinyInteger('day')->nullable();
            $table->boolean('is_open')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();
        });

        Schema::create('schedule_item_hours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('schedule_item_id')->constrained('schedule_items')->onDelete('cascade');
            $table->time('start_at', 0)->nullable();
            $table->time('end_at', 0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_item_hours');
        Schema::dropIfExists('schedule_items');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('schedule_places');
    }
};
