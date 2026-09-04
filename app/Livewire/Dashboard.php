<?php

namespace App\Livewire;

use App\Models\BodyWeight;
use App\Models\Workout;
use App\Repositories\Contracts\BodyWeightRepositoryInterface;
use App\Repositories\Contracts\WorkoutRepositoryInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Vezérlőpult')]
class Dashboard extends Component
{
    protected WorkoutRepositoryInterface $workoutRepository;

    protected BodyWeightRepositoryInterface $bodyWeightRepository;

    public function boot(WorkoutRepositoryInterface $workoutRepository, BodyWeightRepositoryInterface $bodyWeightRepository): void
    {
        $this->workoutRepository = $workoutRepository;
        $this->bodyWeightRepository = $bodyWeightRepository;
    }

    /**
     * Get the authenticated user's most recently logged workout.
     */
    #[Computed]
    public function lastWorkout(): ?Workout
    {
        return $this->workoutRepository->lastForUser(Auth::user());
    }

    /**
     * Get the authenticated user's most recent body weight entries, oldest first.
     *
     * @return EloquentCollection<int, BodyWeight>
     */
    #[Computed]
    public function bodyWeights(): EloquentCollection
    {
        return $this->bodyWeightRepository->recentForUser(Auth::user(), 30)
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
