<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutExerciseSetValue;
use App\Models\WorkoutPlan;
use App\Repositories\Contracts\WorkoutRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WorkoutRepository implements WorkoutRepositoryInterface
{
    public function forUser(User $user): Collection
    {
        return $user->workouts()
            ->with('exercises.exercise', 'exercises.sets.values.field')
            ->latest('performed_at')
            ->latest()
            ->get();
    }

    public function paginatedForUser(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return $user->workouts()
            ->with('exercises.exercise', 'exercises.sets.values.field')
            ->latest('performed_at')
            ->latest()
            ->paginate($perPage);
    }

    public function feedForFollowing(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return Workout::query()
            ->whereIn('user_id', $user->following()->pluck('users.id'))
            ->with('user', 'exercises.exercise', 'exercises.sets.values.field')
            ->latest('performed_at')
            ->latest()
            ->paginate($perPage);
    }

    public function lastForUser(User $user): ?Workout
    {
        return $user->workouts()
            ->with('exercises.exercise', 'exercises.sets.values.field')
            ->latest('performed_at')
            ->latest()
            ->first();
    }

    public function betweenDatesForUser(User $user, Carbon $start, Carbon $end): Collection
    {
        return $user->workouts()
            ->whereBetween('performed_at', [$start, $end])
            ->get();
    }

    public function createFromPlan(User $user, WorkoutPlan $workoutPlan): Workout
    {
        return DB::transaction(function () use ($user, $workoutPlan): Workout {
            $workout = $user->workouts()->create([
                'workout_plan_id' => $workoutPlan->id,
                'name' => $workoutPlan->name,
                'performed_at' => now()->toDateString(),
            ]);

            $workoutPlan->load('exercises.sets.values');

            foreach ($workoutPlan->exercises as $planExercise) {
                $workoutExercise = $workout->exercises()->create([
                    'exercise_id' => $planExercise->exercise_id,
                    'order' => $planExercise->order,
                ]);

                foreach ($planExercise->sets as $planSet) {
                    $workoutSet = $workoutExercise->sets()->create(['order' => $planSet->order]);

                    foreach ($planSet->values as $planValue) {
                        $workoutSet->values()->create([
                            'field_id' => $planValue->field_id,
                            'value' => $planValue->value,
                        ]);
                    }
                }
            }

            return $workout;
        });
    }

    public function updateSetValueCompletion(int $workoutExerciseSetId, int $fieldId, string|int|float|null $completedValue): void
    {
        WorkoutExerciseSetValue::query()
            ->where('workout_exercise_set_id', $workoutExerciseSetId)
            ->where('field_id', $fieldId)
            ->update(['completed_value' => $completedValue === '' ? null : $completedValue]);
    }

    public function updateExercise(Workout $workout, int $workoutExerciseId, array $data): void
    {
        $workout->exercises()->whereKey($workoutExerciseId)->update($data);
    }

    public function delete(Workout $workout): void
    {
        $workout->delete();
    }
}
