<?php

namespace App\Models;

use Database\Factories\WorkoutPlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'name', 'description'])]
class WorkoutPlan extends Model
{
    /** @use HasFactory<WorkoutPlanFactory> */
    use HasFactory;

    /**
     * Get the user that owns the workout plan.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the exercises for the workout plan.
     *
     * @return HasMany<WorkoutPlanExercise, $this>
     */
    public function exercises(): HasMany
    {
        return $this->hasMany(WorkoutPlanExercise::class)->orderBy('order');
    }
}
