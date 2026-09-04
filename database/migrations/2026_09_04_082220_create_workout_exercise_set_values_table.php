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
        Schema::create('workout_exercise_set_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_exercise_set_id')->constrained()->cascadeOnDelete();
            $table->foreignId('field_id')->constrained()->restrictOnDelete();
            $table->decimal('value', 8, 2)->nullable();
            $table->decimal('completed_value', 8, 2)->nullable();
            $table->timestamps();

            $table->unique(['workout_exercise_set_id', 'field_id'], 'wes_values_set_field_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_exercise_set_values');
    }
};
