<?php

namespace App\Livewire\Foods;

use App\Concerns\HasNutritionFields;
use App\Repositories\Contracts\FoodRepositoryInterface;
use App\Repositories\Contracts\NutrientRepositoryInterface;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Új étel')]
class Create extends Component
{
    use HasNutritionFields;

    protected FoodRepositoryInterface $foodRepository;

    public string $name = '';

    public string $calories = '';

    public function boot(FoodRepositoryInterface $foodRepository, NutrientRepositoryInterface $nutrientRepository): void
    {
        $this->foodRepository = $foodRepository;
        $this->nutrientRepository = $nutrientRepository;
    }

    /**
     * Create the food.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:foods,name'],
            'calories' => ['nullable', 'integer', 'min:0', 'max:20000'],
            'barcode' => $this->barcodeRules(),
            ...$this->nutrientValueRules(),
        ]);

        $this->foodRepository->create([
            'name' => $validated['name'],
            'calories' => $validated['calories'] !== '' ? (int) $validated['calories'] : null,
            'barcode' => $validated['barcode'] !== '' && $validated['barcode'] !== null ? $validated['barcode'] : null,
            'nutrition_synced' => $this->nutritionSynced,
        ], $this->nutrientValuesToSync());

        Flux::toast(variant: 'success', text: __('Food created.'));

        $this->redirectRoute('foods.index', navigate: true);
    }
}
