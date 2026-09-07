<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $avatar
 * @property bool $is_admin
 * @property string|null $strava_id
 * @property string|null $strava_token
 * @property string|null $strava_refresh_token
 * @property Carbon|null $strava_token_expires_at
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token', 'strava_token', 'strava_refresh_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable;

    /**
     * The model's default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_admin' => false,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'strava_token_expires_at' => 'datetime',
        ];
    }

    /**
     * Determine whether the user has admin privileges.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Determine whether the user has connected their Strava account.
     */
    public function hasStravaConnected(): bool
    {
        return $this->strava_id !== null;
    }

    /**
     * Get a valid Strava access token for the user, refreshing it first if it has expired.
     */
    public function ensureFreshStravaAccessToken(): ?string
    {
        if (! $this->hasStravaConnected()) {
            return null;
        }

        if ($this->strava_token_expires_at === null || $this->strava_token_expires_at->isFuture()) {
            return $this->strava_token;
        }

        $response = Http::asForm()->post('https://www.strava.com/oauth/token', [
            'client_id' => config('services.strava.client_id'),
            'client_secret' => config('services.strava.client_secret'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->strava_refresh_token,
        ]);

        if ($response->failed()) {
            return null;
        }

        $this->strava_token = $response->json('access_token');
        $this->strava_refresh_token = $response->json('refresh_token');
        $this->strava_token_expires_at = Carbon::createFromTimestamp($response->json('expires_at'));
        $this->save();

        return $this->strava_token;
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * Get the public URL of the user's avatar, if one is set.
     *
     * @return Attribute<string|null, never>
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->avatar ? Storage::disk('public')->url($this->avatar) : null,
        );
    }

    /**
     * Get the workout plans belonging to the user.
     *
     * @return HasMany<WorkoutPlan, $this>
     */
    public function workoutPlans(): HasMany
    {
        return $this->hasMany(WorkoutPlan::class);
    }

    /**
     * Get the workouts logged by the user.
     *
     * @return HasMany<Workout, $this>
     */
    public function workouts(): HasMany
    {
        return $this->hasMany(Workout::class);
    }

    /**
     * Get the body weight entries logged by the user.
     *
     * @return HasMany<BodyWeight, $this>
     */
    public function bodyWeights(): HasMany
    {
        return $this->hasMany(BodyWeight::class);
    }

    /**
     * Get the sleep entries logged by the user.
     *
     * @return HasMany<Sleep, $this>
     */
    public function sleeps(): HasMany
    {
        return $this->hasMany(Sleep::class);
    }

    /**
     * Get the users this user follows.
     *
     * @return BelongsToMany<User, $this>
     */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')->withTimestamps();
    }

    /**
     * Get the users following this user.
     *
     * @return BelongsToMany<User, $this>
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')->withTimestamps();
    }

    /**
     * Determine whether this user is following the given user.
     */
    public function isFollowing(User $user): bool
    {
        return $this->following()->whereKey($user->id)->exists();
    }
}
