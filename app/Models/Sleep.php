<?php

namespace App\Models;

use Database\Factories\SleepFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property Carbon $started_at
 * @property Carbon $ended_at
 * @property int|null $quality
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'started_at', 'ended_at', 'quality', 'notes'])]
class Sleep extends Model
{
    /** @use HasFactory<SleepFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'quality' => 'integer',
        ];
    }

    /**
     * Get the sleep duration in minutes.
     */
    public function durationInMinutes(): int
    {
        return $this->started_at->diffInMinutes($this->ended_at);
    }

    /**
     * Get the user that owns the sleep entry.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
