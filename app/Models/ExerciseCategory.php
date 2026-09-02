<?php

namespace App\Models;

use Database\Factories\ExerciseCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name'])]
class ExerciseCategory extends Model
{
    /** @use HasFactory<ExerciseCategoryFactory> */
    use HasFactory;

    /**
     * Get the exercises in this category.
     *
     * @return HasMany<Exercise, $this>
     */
    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class, 'category_id');
    }
}
