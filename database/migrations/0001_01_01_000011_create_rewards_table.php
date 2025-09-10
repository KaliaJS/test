<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->unsignedMediumInteger('total_points');
            $table->timestamps();
        });

        Schema::create('reward_points', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('reward_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('order_id')->nullable()->constrained('orders')->onDelete('cascade');
            $table->unsignedSmallInteger('points');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reward_points');
        Schema::dropIfExists('rewards');
    }
};
