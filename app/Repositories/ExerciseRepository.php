<?php

namespace App\Repositories;

use App\Models\Exercise;
use App\Repositories\Contracts\ExerciseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ExerciseRepository implements ExerciseRepositoryInterface
{
    public function allGroupedByCategory(): Collection
    {
        $uncategorized = __('Uncategorized');

        $grouped = Exercise::query()
            ->with('category', 'exerciseType')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Exercise $exercise) => $exercise->category?->name ?? $uncategorized);

        $others = $grouped->pull($uncategorized);

        $grouped = $grouped->sortKeys();

        if ($others) {
            $grouped->put($uncategorized, $others);
        }

        return $grouped;
    }

    public function allWithExerciseType(): Collection
    {
        return Exercise::query()->with('exerciseType.fields.field')->orderBy('name')->get();
    }

    public function create(array $data): Exercise
    {
        return Exercise::query()->create($data);
    }

    public function update(Exercise $exercise, array $data): Exercise
    {
        $exercise->update($data);

        return $exercise;
    }

    public function delete(Exercise $exercise): void
    {
        $exercise->delete();
    }
}
