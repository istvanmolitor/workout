<?php

namespace App\Models;

use Database\Factories\WorkoutExerciseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $workout_id
 * @property int $exercise_id
 * @property int|null $difficulty
 * @property string|null $note
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['workout_id', 'exercise_id', 'difficulty', 'note', 'order'])]
class WorkoutExercise extends Model
{
    /** @use HasFactory<WorkoutExerciseFactory> */
    use HasFactory;

    /**
     * Get the workout that owns the exercise.
     *
     * @return BelongsTo<Workout, $this>
     */
    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
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

    /**
     * Get the sets performed for this exercise.
     *
     * @return HasMany<WorkoutExerciseSet, $this>
     */
    public function sets(): HasMany
    {
        return $this->hasMany(WorkoutExerciseSet::class)->orderBy('order');
    }

    /**
     * Get the translated labels for each difficulty level, keyed by level.
     *
     * @return array<int, string>
     */
    public static function difficultyLabels(): array
    {
        return [
            1 => __('Very easy'),
            2 => __('Easy'),
            3 => __('Medium'),
            4 => __('Hard'),
            5 => __('Very hard'),
        ];
    }
}
