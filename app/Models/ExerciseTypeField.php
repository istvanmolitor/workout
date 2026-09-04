<?php

namespace App\Models;

use Database\Factories\ExerciseTypeFieldFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $exercise_type_id
 * @property int $field_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Field $field
 */
#[Fillable(['exercise_type_id', 'field_id', 'order'])]
class ExerciseTypeField extends Model
{
    /** @use HasFactory<ExerciseTypeFieldFactory> */
    use HasFactory;

    /**
     * Get the exercise type this entry belongs to.
     *
     * @return BelongsTo<ExerciseType, $this>
     */
    public function exerciseType(): BelongsTo
    {
        return $this->belongsTo(ExerciseType::class);
    }

    /**
     * Get the field tracked by this entry.
     *
     * @return BelongsTo<Field, $this>
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
