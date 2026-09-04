<?php

namespace App\Models;

use Database\Factories\WorkoutExerciseSetValueFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $workout_exercise_set_id
 * @property int $field_id
 * @property string|null $value
 * @property string|null $completed_value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Field $field
 */
#[Fillable(['workout_exercise_set_id', 'field_id', 'value', 'completed_value'])]
class WorkoutExerciseSetValue extends Model
{
    /** @use HasFactory<WorkoutExerciseSetValueFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'completed_value' => 'decimal:2',
        ];
    }

    /**
     * Get the set this value belongs to.
     *
     * @return BelongsTo<WorkoutExerciseSet, $this>
     */
    public function workoutExerciseSet(): BelongsTo
    {
        return $this->belongsTo(WorkoutExerciseSet::class);
    }

    /**
     * Get the field this value is for.
     *
     * @return BelongsTo<Field, $this>
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
