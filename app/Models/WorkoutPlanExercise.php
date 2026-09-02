<?php

namespace App\Models;

use Database\Factories\WorkoutPlanExerciseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $workout_plan_id
 * @property int $exercise_id
 * @property int $sets
 * @property int $reps
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['workout_plan_id', 'exercise_id', 'sets', 'reps', 'order'])]
class WorkoutPlanExercise extends Model
{
    /** @use HasFactory<WorkoutPlanExerciseFactory> */
    use HasFactory;

    /**
     * Get the workout plan that owns the exercise.
     *
     * @return BelongsTo<WorkoutPlan, $this>
     */
    public function workoutPlan(): BelongsTo
    {
        return $this->belongsTo(WorkoutPlan::class);
    }

    /**
     * Get the catalog exercise this entry refers to.
     *
     * @return BelongsTo<Exercise, $this>
     */
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}
