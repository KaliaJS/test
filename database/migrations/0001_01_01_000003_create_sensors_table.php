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
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();
            $table->string('mac', 17)->unique();
            $table->string('name', 20)->unique()->nullable();
            $table->unsignedSmallInteger('battery_mv')->nullable();
            $table->decimal('last_temp', 5, 2)->nullable();
            $table->decimal('min_temp_alert', 5, 2)->nullable();
            $table->decimal('max_temp_alert', 5, 2)->nullable();
            $table->tinyInteger('type')->nullable();
            $table->timestamps();
        });

        Schema::create('sensor_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')->constrained()->onDelete('cascade');
            $table->unsignedSmallInteger('sequence')->nullable();
            $table->decimal('temp', 5, 2);
            $table->timestamp('measured_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_measurements');
        Schema::dropIfExists('sensors');
    }
};
