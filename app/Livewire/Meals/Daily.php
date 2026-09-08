<?php

namespace App\Livewire\Meals;

use App\Models\Food;
use App\Models\Meal;
use App\Models\Nutrient;
use App\Repositories\Contracts\MealRepositoryInterface;
use App\Repositories\Contracts\NutrientRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Napi étkezés')]
class Daily extends Component
{
    /**
     * The meal types shown as separate sections, in display order. Meals without one of
     * these types (or without a type at all) are grouped under the empty-string key.
     *
     * @var array<int, string>
     */
    private const TYPES = ['breakfast', 'lunch', 'dinner', 'snack'];

    protected MealRepositoryInterface $mealRepository;

    protected NutrientRepositoryInterface $nutrientRepository;

    public string $date;

    public function boot(MealRepositoryInterface $mealRepository, NutrientRepositoryInterface $nutrientRepository): void
    {
        $this->mealRepository = $mealRepository;
        $this->nutrientRepository = $nutrientRepository;
    }

    public function mount(?string $date = null): void
    {
        if ($date === null) {
            $this->date = Carbon::now()->toDateString();

            return;
        }

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
        $this->redirectRoute('meals.daily', ['date' => $this->day()->subDay()->toDateString()], navigate: true);
    }

    /**
     * Move to the next day.
     */
    public function nextDay(): void
    {
        $this->redirectRoute('meals.daily', ['date' => $this->day()->addDay()->toDateString()], navigate: true);
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
     * Get the authenticated user's meal entries eaten on this day, with their foods and the
     * foods' nutrient values eager loaded.
     *
     * @return Collection<int, Meal>
     */
    #[Computed]
    public function meals(): Collection
    {
        $meals = $this->mealRepository->forUserOnDate(Auth::user(), $this->day());

        $meals->load('foods.nutrients');

        return $meals;
    }

    /**
     * Get the day's meal entries grouped by type, in display order. Meals without a
     * recognised type are grouped under the empty-string key.
     *
     * @return SupportCollection<string, Collection<int, Meal>>
     */
    #[Computed]
    public function mealsByType(): SupportCollection
    {
        $groups = $this->meals->groupBy(fn (Meal $meal) => in_array($meal->type, self::TYPES, true) ? $meal->type : '');

        return collect([...self::TYPES, ''])
            ->mapWithKeys(fn (string $type) => [$type => $groups->get($type, new Collection)])
            ->filter(fn (Collection $group) => $group->isNotEmpty());
    }

    /**
     * Get a human-readable label for a meal type group.
     */
    public function typeLabel(string $type): string
    {
        return match ($type) {
            'breakfast' => __('Breakfast'),
            'lunch' => __('Lunch'),
            'dinner' => __('Dinner'),
            'snack' => __('Snack'),
            default => __('Other'),
        };
    }

    /**
     * Get the total calories eaten on this day, scaled by each food's quantity.
     */
    #[Computed]
    public function totalCalories(): int
    {
        return (int) round($this->allFoods()->sum($this->scaledCalories(...)));
    }

    /**
     * Get the calories eaten in a single meal, scaled by each food's quantity.
     */
    public function mealCalories(Meal $meal): int
    {
        return (int) round($meal->foods->sum($this->scaledCalories(...)));
    }

    /**
     * Get the total amount of each nutrient eaten on this day, scaled by each food's
     * quantity. Only nutrients with a non-zero total are included.
     *
     * @return SupportCollection<int, array{nutrient: Nutrient, total: float}>
     */
    #[Computed]
    public function nutrientTotals(): SupportCollection
    {
        $foods = $this->allFoods();

        return $this->nutrientRepository->all()
            ->map(fn (Nutrient $nutrient) => [
                'nutrient' => $nutrient,
                'total' => $foods->sum(fn (Food $food) => $this->scaledNutrientValue($food, $nutrient)),
            ])
            ->filter(fn (array $row) => $row['total'] > 0)
            ->values();
    }

    /**
     * Get every food eaten on this day, across all meals.
     *
     * @return SupportCollection<int, Food>
     */
    private function allFoods(): SupportCollection
    {
        return $this->meals->flatMap->foods;
    }

    /**
     * Scale a food's per-100g calories by the quantity eaten (in grams).
     */
    private function scaledCalories(Food $food): float
    {
        if ($food->calories === null || $food->pivot->quantity === null) {
            return 0;
        }

        return $food->calories * (float) $food->pivot->quantity / 100;
    }

    /**
     * Scale a food's per-100g nutrient value by the quantity eaten (in grams).
     */
    private function scaledNutrientValue(Food $food, Nutrient $nutrient): float
    {
        if ($food->pivot->quantity === null) {
            return 0;
        }

        $value = $food->nutrients->firstWhere('id', $nutrient->id)?->pivot->value;

        if ($value === null) {
            return 0;
        }

        return (float) $value * (float) $food->pivot->quantity / 100;
    }
}
