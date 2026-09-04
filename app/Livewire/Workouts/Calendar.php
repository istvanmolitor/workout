<?php

namespace App\Livewire\Workouts;

use App\Models\Workout;
use App\Repositories\Contracts\WorkoutRepositoryInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edzésnaptár')]
class Calendar extends Component
{
    protected WorkoutRepositoryInterface $workoutRepository;

    public int $year;

    public int $month;

    public function boot(WorkoutRepositoryInterface $workoutRepository): void
    {
        $this->workoutRepository = $workoutRepository;
    }

    public function mount(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
    }

    /**
     * Move the calendar back to the previous month.
     */
    public function previousMonth(): void
    {
        $this->setMonth($this->currentMonth()->subMonthNoOverflow());
    }

    /**
     * Move the calendar forward to the next month.
     */
    public function nextMonth(): void
    {
        $this->setMonth($this->currentMonth()->addMonthNoOverflow());
    }

    /**
     * Jump the calendar back to the current month.
     */
    public function goToToday(): void
    {
        $this->setMonth(Carbon::now());
    }

    private function setMonth(Carbon $date): void
    {
        $this->year = $date->year;
        $this->month = $date->month;
    }

    private function currentMonth(): Carbon
    {
        return Carbon::create($this->year, $this->month, 1);
    }

    /**
     * Get a human-readable label for the visible month, e.g. "2026. szeptember".
     */
    #[Computed]
    public function monthLabel(): string
    {
        return $this->currentMonth()->translatedFormat('Y. F');
    }

    /**
     * Get the calendar grid for the visible month as Monday-first weeks.
     *
     * @return array<int, array<int, Carbon>>
     */
    #[Computed]
    public function weeks(): array
    {
        $start = $this->currentMonth()->startOfWeek(Carbon::MONDAY);
        $end = $this->currentMonth()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $days = [];
        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $days[] = $day->copy();
        }

        return array_chunk($days, 7);
    }

    /**
     * Get the authenticated user's workouts visible in the calendar grid, keyed by date.
     *
     * @return Collection<string, EloquentCollection<int, Workout>>
     */
    #[Computed]
    public function workoutsByDate(): Collection
    {
        $weeks = $this->weeks;
        $start = $weeks[0][0];
        $end = $weeks[count($weeks) - 1][6];

        return $this->workoutRepository->betweenDatesForUser(Auth::user(), $start, $end)
            ->groupBy(fn (Workout $workout) => $workout->performed_at->format('Y-m-d'));
    }
}
