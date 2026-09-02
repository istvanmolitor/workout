<?php

namespace App\Models;

use Database\Factories\ExerciseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property int|null $category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ExerciseCategory|null $category
 */
#[Fillable(['name', 'category_id'])]
class Exercise extends Model
{
    /** @use HasFactory<ExerciseFactory> */
    use HasFactory;

    /**
     * Get the category this exercise belongs to.
     *
     * @return BelongsTo<ExerciseCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExerciseCategory::class, 'category_id');
    }

    /**
     * Get the workout plan exercises that use this exercise.
     *
     * @return HasMany<WorkoutPlanExercise, $this>
     */
    public function workoutPlanExercises(): HasMany
    {
        return $this->hasMany(WorkoutPlanExercise::class);
    }
}
