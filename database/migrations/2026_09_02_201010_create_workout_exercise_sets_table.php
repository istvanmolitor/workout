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
        Schema::create('workout_exercise_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_exercise_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('reps');
            $table->unsignedInteger('completed_reps')->nullable();
            $table->decimal('weight', 6, 2)->nullable();
            $table->decimal('completed_weight', 6, 2)->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index('workout_exercise_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_exercise_sets');
    }
};
