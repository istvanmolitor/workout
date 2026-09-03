<?php

namespace App\Models;

use Database\Factories\BodyWeightFactory;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $weight
 * @property Carbon $measured_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'weight', 'measured_at'])]
class BodyWeight extends Model
{
    /** @use HasFactory<BodyWeightFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
        ];
    }

    /**
     * Interact with the measured_at attribute, storing it as a plain Y-m-d
     * string so it matches the raw date used by uniqueness validation.
     *
     * @return Attribute<Carbon, string|DateTimeInterface>
     */
    protected function measuredAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value): Carbon => Carbon::parse($value),
            set: fn (string|DateTimeInterface $value): string => Carbon::parse($value)->format('Y-m-d'),
        );
    }

    /**
     * Get the user that owns the body weight entry.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
