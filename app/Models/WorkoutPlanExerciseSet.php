<?php

namespace App\Models;

use Database\Factories\WorkoutPlanExerciseSetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $workout_plan_exercise_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['workout_plan_exercise_id', 'order'])]
class WorkoutPlanExerciseSet extends Model
{
    /** @use HasFactory<WorkoutPlanExerciseSetFactory> */
    use HasFactory;

    /**
     * Get the workout plan exercise that owns the set.
     *
     * @return BelongsTo<WorkoutPlanExercise, $this>
     */
    public function workoutPlanExercise(): BelongsTo
    {
        return $this->belongsTo(WorkoutPlanExercise::class);
    }

    /**
     * Get the field values recorded for this set.
     *
     * @return HasMany<WorkoutPlanExerciseSetValue, $this>
     */
    public function values(): HasMany
    {
        return $this->hasMany(WorkoutPlanExerciseSetValue::class);
    }
}
