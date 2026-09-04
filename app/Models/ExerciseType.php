<?php

namespace App\Models;

use Database\Factories\ExerciseTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property bool $single_set
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'single_set'])]
class ExerciseType extends Model
{
    /** @use HasFactory<ExerciseTypeFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'single_set' => 'boolean',
        ];
    }

    /**
     * Get the exercises of this type.
     *
     * @return HasMany<Exercise, $this>
     */
    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    /**
     * Get the fields that exercises of this type track, in display order.
     *
     * @return HasMany<ExerciseTypeField, $this>
     */
    public function fields(): HasMany
    {
        return $this->hasMany(ExerciseTypeField::class)->orderBy('order');
    }
}
