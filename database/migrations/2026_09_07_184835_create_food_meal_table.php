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
        Schema::create('food_meal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_id')->constrained('foods')->restrictOnDelete();
            $table->foreignId('meal_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['food_id', 'meal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_meal');
    }
};
