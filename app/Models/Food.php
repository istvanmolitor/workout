<?php

namespace App\Models;

use Database\Factories\FoodFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property int|null $calories
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Meal> $meals
 */
#[Fillable(['name', 'calories'])]
class Food extends Model
{
    /** @use HasFactory<FoodFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'foods';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'calories' => 'integer',
        ];
    }

    /**
     * Get the meal entries this food was eaten in.
     *
     * @return BelongsToMany<Meal, $this>
     */
    public function meals(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class);
    }
}
