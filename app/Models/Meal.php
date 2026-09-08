<?php

namespace App\Models;

use Database\Factories\MealFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property Carbon $eaten_at
 * @property string|null $type
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Food> $foods
 */
#[Fillable(['user_id', 'eaten_at', 'type', 'notes'])]
class Meal extends Model
{
    /** @use HasFactory<MealFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'eaten_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the meal entry.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the foods eaten in this meal entry.
     *
     * @return BelongsToMany<Food, $this>
     */
    public function foods(): BelongsToMany
    {
        return $this->belongsToMany(Food::class)->withPivot('quantity')->withTimestamps();
    }
}
