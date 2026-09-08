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
        Schema::table('food_meal', function (Blueprint $table) {
            $table->decimal('quantity', 8, 2)->unsigned()->nullable()->after('meal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_meal', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
