<?php

namespace App\Livewire\Calendar;

use App\Models\BodyWeight;
use App\Models\Meal;
use App\Models\Sleep;
use App\Models\Workout;
use App\Repositories\Contracts\BodyWeightRepositoryInterface;
use App\Repositories\Contracts\MealRepositoryInterface;
use App\Repositories\Contracts\SleepRepositoryInterface;
use App\Repositories\Contracts\WorkoutRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Nap részletei')]
class Show extends Component
{
    protected WorkoutRepositoryInterface $workoutRepository;

    protected BodyWeightRepositoryInterface $bodyWeightRepository;

    protected SleepRepositoryInterface $sleepRepository;

    protected MealRepositoryInterface $mealRepository;

    public string $date;

    public function boot(
        WorkoutRepositoryInterface $workoutRepository,
        BodyWeightRepositoryInterface $bodyWeightRepository,
        SleepRepositoryInterface $sleepRepository,
        MealRepositoryInterface $mealRepository,
    ): void {
        $this->workoutRepository = $workoutRepository;
        $this->bodyWeightRepository = $bodyWeightRepository;
        $this->sleepRepository = $sleepRepository;
        $this->mealRepository = $mealRepository;
    }

    public function mount(string $date): void
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d') !== $date) {
            abort(404);
        }

        $this->date = $date;
    }

    /**
     * Move to the previous day.
     */
    public function previousDay(): void
    {
        $this->redirectRoute('calendar.show', ['date' => $this->day()->subDay()->toDateString()], navigate: true);
    }

    /**
     * Move to the next day.
     */
    public function nextDay(): void
    {
        $this->redirectRoute('calendar.show', ['date' => $this->day()->addDay()->toDateString()], navigate: true);
    }

    private function day(): Carbon
    {
        return Carbon::createFromFormat('Y-m-d', $this->date)->startOfDay();
    }

    /**
     * Get a human-readable label for the visible day, e.g. "2026. szeptember 6.".
     */
    #[Computed]
    public function dayLabel(): string
    {
        return $this->day()->translatedFormat('Y. F j.');
    }

    /**
     * Get the authenticated user's workouts performed on this day.
     *
     * @return Collection<int, Workout>
     */
    #[Computed]
    public function workouts(): Collection
    {
        $day = $this->day();

        return $this->workoutRepository->betweenDatesForUser(Auth::user(), $day->copy()->startOfDay(), $day->copy()->endOfDay());
    }

    /**
     * Get the authenticated user's body weight entry measured on this day.
     */
    #[Computed]
    public function bodyWeight(): ?BodyWeight
    {
        return $this->bodyWeightRepository->forUserOnDate(Auth::user(), $this->day());
    }

    /**
     * Get the authenticated user's sleep entries that started or ended on this day.
     *
     * @return Collection<int, Sleep>
     */
    #[Computed]
    public function sleeps(): Collection
    {
        return $this->sleepRepository->forUserOnDate(Auth::user(), $this->day());
    }

    /**
     * Get the authenticated user's meal entries eaten on this day.
     *
     * @return Collection<int, Meal>
     */
    #[Computed]
    public function meals(): Collection
    {
        return $this->mealRepository->forUserOnDate(Auth::user(), $this->day());
    }
}
