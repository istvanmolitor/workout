<?php

namespace App\Repositories;

use App\Models\ExerciseType;
use App\Repositories\Contracts\ExerciseTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ExerciseTypeRepository implements ExerciseTypeRepositoryInterface
{
    public function all(): Collection
    {
        return ExerciseType::query()->orderBy('name')->get();
    }

    public function allWithFields(): Collection
    {
        return ExerciseType::query()->with('fields.field')->orderBy('name')->get();
    }

    public function create(array $data, array $fields): ExerciseType
    {
        return DB::transaction(function () use ($data, $fields): ExerciseType {
            $exerciseType = ExerciseType::query()->create($data);

            $this->syncFields($exerciseType, $fields);

            return $exerciseType;
        });
    }

    public function update(ExerciseType $exerciseType, array $data, array $fields): ExerciseType
    {
        DB::transaction(function () use ($exerciseType, $data, $fields): void {
            $exerciseType->update($data);

            $exerciseType->fields()->delete();

            $this->syncFields($exerciseType, $fields);
        });

        return $exerciseType;
    }

    public function delete(ExerciseType $exerciseType): void
    {
        $exerciseType->delete();
    }

    /**
     * @param  array<int, array{field_id: int}>  $fields
     */
    private function syncFields(ExerciseType $exerciseType, array $fields): void
    {
        foreach ($fields as $order => $field) {
            $exerciseType->fields()->create([
                'field_id' => $field['field_id'],
                'order' => $order,
            ]);
        }
    }
}
