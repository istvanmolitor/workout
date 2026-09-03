<?php

namespace App\Livewire;

use App\Models\BodyWeight;
use App\Models\Workout;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Vezérlőpult')]
class Dashboard extends Component
{
    public int $year;

    public int $month;

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

        return Auth::user()->workouts()
            ->whereBetween('performed_at', [$start, $end])
            ->get()
            ->groupBy(fn (Workout $workout) => $workout->performed_at->format('Y-m-d'));
    }

    /**
     * Get the authenticated user's most recent body weight entries, oldest first.
     *
     * @return EloquentCollection<int, BodyWeight>
     */
    #[Computed]
    public function bodyWeights(): EloquentCollection
    {
        return Auth::user()->bodyWeights()
            ->latest('measured_at')
            ->limit(30)
            ->get()
            ->sortBy('measured_at')
            ->values();
    }

    /**
     * Get the SVG polyline points for the body weight chart, scaled to a 0-100 x 0-40 viewBox.
     *
     * @return array<int, array{x: float, y: float}>
     */
    #[Computed]
    public function bodyWeightChartPoints(): array
    {
        $bodyWeights = $this->bodyWeights;
        $count = $bodyWeights->count();

        if ($count < 2) {
            return [];
        }

        $values = $bodyWeights->map(fn (BodyWeight $entry) => (float) $entry->weight);
        $min = $values->min();
        $range = max($values->max() - $min, 0.01);

        return $bodyWeights
            ->map(fn (BodyWeight $entry, int $index) => [
                'x' => ($index / ($count - 1)) * 100,
                'y' => 40 - ((((float) $entry->weight) - $min) / $range) * 40,
            ])
            ->values()
            ->all();
    }
}
