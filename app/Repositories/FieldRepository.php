<?php

namespace App\Repositories;

use App\Models\Field;
use App\Repositories\Contracts\FieldRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FieldRepository implements FieldRepositoryInterface
{
    public function all(): Collection
    {
        return Field::query()->orderBy('name')->get();
    }

    public function create(array $data): Field
    {
        return Field::query()->create($data);
    }

    public function update(Field $field, array $data): Field
    {
        $field->update($data);

        return $field;
    }

    public function delete(Field $field): void
    {
        $field->delete();
    }
}
