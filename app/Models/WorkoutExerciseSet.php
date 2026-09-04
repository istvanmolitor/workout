<?php

namespace App\Models;

use Database\Factories\WorkoutExerciseSetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $workout_exercise_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['workout_exercise_id', 'order'])]
class WorkoutExerciseSet extends Model
{
    /** @use HasFactory<WorkoutExerciseSetFactory> */
    use HasFactory;

    /**
     * Get the workout exercise that owns the set.
     *
     * @return BelongsTo<WorkoutExercise, $this>
     */
    public function workoutExercise(): BelongsTo
    {
        return $this->belongsTo(WorkoutExercise::class);
    }

    /**
     * Get the field values recorded for this set.
     *
     * @return HasMany<WorkoutExerciseSetValue, $this>
     */
    public function values(): HasMany
    {
        return $this->hasMany(WorkoutExerciseSetValue::class);
    }
}
