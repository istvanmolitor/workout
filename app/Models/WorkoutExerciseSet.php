<?php

namespace App\Models;

use Database\Factories\WorkoutExerciseSetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $workout_exercise_id
 * @property int $reps
 * @property int|null $completed_reps
 * @property string|null $weight
 * @property string|null $completed_weight
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['workout_exercise_id', 'reps', 'completed_reps', 'weight', 'completed_weight', 'order'])]
class WorkoutExerciseSet extends Model
{
    /** @use HasFactory<WorkoutExerciseSetFactory> */
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
            'completed_weight' => 'decimal:2',
        ];
    }

    /**
     * Get the workout exercise that owns the set.
     *
     * @return BelongsTo<WorkoutExercise, $this>
     */
    public function workoutExercise(): BelongsTo
    {
        return $this->belongsTo(WorkoutExercise::class);
    }
}
