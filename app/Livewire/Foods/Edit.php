<?php

namespace App\Livewire\Foods;

use App\Models\Food;
use App\Repositories\Contracts\FoodRepositoryInterface;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Étel szerkesztése')]
class Edit extends Component
{
    protected FoodRepositoryInterface $foodRepository;

    #[Locked]
    public Food $food;

    public string $name = '';

    public string $calories = '';

    public function boot(FoodRepositoryInterface $foodRepository): void
    {
        $this->foodRepository = $foodRepository;
    }

    /**
     * Mount the component.
     */
    public function mount(Food $food): void
    {
        $this->food = $food;
        $this->name = $food->name;
        $this->calories = (string) $food->calories;
    }

    /**
     * Update the food.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('foods', 'name')->ignore($this->food->id)],
            'calories' => ['nullable', 'integer', 'min:0', 'max:20000'],
        ]);

        $this->foodRepository->update($this->food, [
            'name' => $validated['name'],
            'calories' => $validated['calories'] !== '' ? (int) $validated['calories'] : null,
        ]);

        Flux::toast(variant: 'success', text: __('Food updated.'));

        $this->redirectRoute('foods.index', navigate: true);
    }
}
