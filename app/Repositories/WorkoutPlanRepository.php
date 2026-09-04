<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\WorkoutPlan;
use App\Models\WorkoutPlanExercise;
use App\Repositories\Contracts\WorkoutPlanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WorkoutPlanRepository implements WorkoutPlanRepositoryInterface
{
    public function forUser(User $user): Collection
    {
        return $user->workoutPlans()->with('exercises.exercise', 'exercises.sets.values.field')->latest()->get();
    }

    public function create(User $user, string $name, ?string $description, array $exercises): WorkoutPlan
    {
        return DB::transaction(function () use ($user, $name, $description, $exercises): WorkoutPlan {
            $workoutPlan = $user->workoutPlans()->create([
                'name' => $name,
                'description' => $description,
            ]);

            $this->syncExercises($workoutPlan, $exercises);

            return $workoutPlan;
        });
    }

    public function update(WorkoutPlan $workoutPlan, string $name, ?string $description, array $exercises): WorkoutPlan
    {
        DB::transaction(function () use ($workoutPlan, $name, $description, $exercises): void {
            $workoutPlan->update([
                'name' => $name,
                'description' => $description,
            ]);

            $workoutPlan->exercises()->delete();

            $this->syncExercises($workoutPlan, $exercises);
        });

        return $workoutPlan;
    }

    public function delete(WorkoutPlan $workoutPlan): void
    {
        $workoutPlan->delete();
    }

    /**
     * @param  array<int, array{exercise_id: int|string, sets: array<int, array{values: array<int, int|string|null>}>}>  $exercises
     */
    private function syncExercises(WorkoutPlan $workoutPlan, array $exercises): void
    {
        foreach ($exercises as $order => $exercise) {
            /** @var WorkoutPlanExercise $planExercise */
            $planExercise = $workoutPlan->exercises()->create([
                'exercise_id' => $exercise['exercise_id'],
                'order' => $order,
            ]);

            foreach ($exercise['sets'] as $setOrder => $set) {
                $planSet = $planExercise->sets()->create(['order' => $setOrder]);

                foreach ($set['values'] as $fieldId => $value) {
                    if ($value === null || $value === '') {
                        continue;
                    }

                    $planSet->values()->create(['field_id' => $fieldId, 'value' => $value]);
                }
            }
        }
    }
}
