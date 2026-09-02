<?php

namespace App\Models;

use Database\Factories\WorkoutPlanExerciseSetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $workout_plan_exercise_id
 * @property int $reps
 * @property string|null $weight
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['workout_plan_exercise_id', 'reps', 'weight', 'order'])]
class WorkoutPlanExerciseSet extends Model
{
    /** @use HasFactory<WorkoutPlanExerciseSetFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
        ];
    }

    /**
     * Get the workout plan exercise that owns the set.
     *
     * @return BelongsTo<WorkoutPlanExercise, $this>
     */
    public function workoutPlanExercise(): BelongsTo
    {
        return $this->belongsTo(WorkoutPlanExercise::class);
    }
}
