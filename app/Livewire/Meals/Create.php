<?php

namespace App\Livewire\Meals;

use App\Models\Food;
use App\Repositories\Contracts\FoodRepositoryInterface;
use App\Repositories\Contracts\MealRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Új étkezés')]
class Create extends Component
{
    protected MealRepositoryInterface $mealRepository;

    protected FoodRepositoryInterface $foodRepository;

    /**
     * @var array<int, int>
     */
    public array $foodIds = [];

    public string $foodSearch = '';

    public string $newFoodCalories = '';

    public string $eaten_at = '';

    public string $type = '';

    public string $notes = '';

    public function boot(MealRepositoryInterface $mealRepository, FoodRepositoryInterface $foodRepository): void
    {
        $this->mealRepository = $mealRepository;
        $this->foodRepository = $foodRepository;
    }

    /**
     * Get the foods currently added to this meal, ordered by name.
     *
     * @return Collection<int, Food>
     */
    #[Computed]
    public function selectedFoods(): Collection
    {
        if ($this->foodIds === []) {
            return new Collection;
        }

        return Food::query()->whereIn('id', $this->foodIds)->orderBy('name')->get();
    }

    /**
     * Get the foods matching the current search term, excluding ones already added.
     *
     * @return Collection<int, Food>
     */
    #[Computed]
    public function foodResults(): Collection
    {
        if (trim($this->foodSearch) === '') {
            return new Collection;
        }

        return $this->foodRepository->search(trim($this->foodSearch))
            ->reject(fn (Food $food) => in_array($food->id, $this->foodIds, true))
            ->values();
    }

    /**
     * Add a food from the search results to this meal.
     */
    public function addFood(Food $food): void
    {
        if (! in_array($food->id, $this->foodIds, true)) {
            $this->foodIds[] = $food->id;
        }

        $this->foodSearch = '';
    }

    /**
     * Remove a food from this meal.
     */
    public function removeFood(int $foodId): void
    {
        $this->foodIds = array_values(array_filter($this->foodIds, fn (int $id) => $id !== $foodId));
    }

    /**
     * Add the searched term as a new food and attach it to this meal.
     */
    public function createFood(): void
    {
        $name = trim($this->foodSearch);

        if ($name === '') {
            return;
        }

        $validated = $this->validate([
            'newFoodCalories' => ['nullable', 'integer', 'min:0', 'max:20000'],
        ]);

        $food = $this->foodRepository->firstOrCreate(
            $name,
            $validated['newFoodCalories'] !== '' ? (int) $validated['newFoodCalories'] : null,
        );

        $this->addFood($food);
        $this->newFoodCalories = '';
    }

    /**
     * Log a new meal entry.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'foodIds' => ['required', 'array', 'min:1'],
            'foodIds.*' => ['integer', 'exists:foods,id'],
            'eaten_at' => ['required', 'date', 'before_or_equal:now'],
            'type' => ['nullable', 'string', 'in:breakfast,lunch,dinner,snack'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->mealRepository->create(Auth::user(), [
            'eaten_at' => $validated['eaten_at'],
            'type' => $validated['type'],
            'notes' => $validated['notes'],
        ], $validated['foodIds']);

        Flux::toast(variant: 'success', text: __('Meal logged.'));

        $this->redirectRoute('meals.index', navigate: true);
    }
}
