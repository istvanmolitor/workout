<?php

namespace App\Livewire\Foods;

use App\Repositories\Contracts\FoodRepositoryInterface;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Új étel')]
class Create extends Component
{
    protected FoodRepositoryInterface $foodRepository;

    public string $name = '';

    public string $calories = '';

    public function boot(FoodRepositoryInterface $foodRepository): void
    {
        $this->foodRepository = $foodRepository;
    }

    /**
     * Create the food.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:foods,name'],
            'calories' => ['nullable', 'integer', 'min:0', 'max:20000'],
        ]);

        $this->foodRepository->create([
            'name' => $validated['name'],
            'calories' => $validated['calories'] !== '' ? (int) $validated['calories'] : null,
        ]);

        Flux::toast(variant: 'success', text: __('Food created.'));

        $this->redirectRoute('foods.index', navigate: true);
    }
}
