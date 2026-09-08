<?php

namespace App\Livewire\Foods;

use App\Concerns\HasNutritionFields;
use App\Models\Food;
use App\Repositories\Contracts\FoodRepositoryInterface;
use App\Repositories\Contracts\NutrientRepositoryInterface;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Étel szerkesztése')]
class Edit extends Component
{
    use HasNutritionFields;

    protected FoodRepositoryInterface $foodRepository;

    #[Locked]
    public Food $food;

    public string $name = '';

    public string $calories = '';

    public function boot(FoodRepositoryInterface $foodRepository, NutrientRepositoryInterface $nutrientRepository): void
    {
        $this->foodRepository = $foodRepository;
        $this->nutrientRepository = $nutrientRepository;
    }

    /**
     * Mount the component.
     */
    public function mount(Food $food): void
    {
        $this->food = $food->load('nutrients');
        $this->name = $food->name;
        $this->calories = (string) $food->calories;
        $this->fillNutritionFields($food);
    }

    /**
     * Update the food.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('foods', 'name')->ignore($this->food->id)],
            'calories' => ['nullable', 'integer', 'min:0', 'max:20000'],
            'barcode' => $this->barcodeRules($this->food->id),
            ...$this->nutrientValueRules(),
        ]);

        $this->foodRepository->update($this->food, [
            'name' => $validated['name'],
            'calories' => $validated['calories'] !== '' ? (int) $validated['calories'] : null,
            'barcode' => $validated['barcode'] !== '' && $validated['barcode'] !== null ? $validated['barcode'] : null,
            'nutrition_synced' => $this->nutritionSynced,
        ], $this->nutrientValuesToSync());

        Flux::toast(variant: 'success', text: __('Food updated.'));

        $this->redirectRoute('foods.index', navigate: true);
    }
}
