<?php

namespace App\Livewire\Nutrients;

use App\Repositories\Contracts\NutrientRepositoryInterface;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Új tápanyag')]
class Create extends Component
{
    protected NutrientRepositoryInterface $nutrientRepository;

    public string $name = '';

    public string $slug = '';

    public string $unit = '';

    public string $decimal_places = '2';

    public string $sort_order = '0';

    public function boot(NutrientRepositoryInterface $nutrientRepository): void
    {
        $this->nutrientRepository = $nutrientRepository;
    }

    /**
     * Create the nutrient in the catalog.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:nutrients,name'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', 'unique:nutrients,slug'],
            'unit' => ['required', 'string', 'max:20'],
            'decimal_places' => ['required', 'integer', 'min:0', 'max:6'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $this->nutrientRepository->create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'unit' => $validated['unit'],
            'decimal_places' => (int) $validated['decimal_places'],
            'sort_order' => (int) $validated['sort_order'],
        ]);

        Flux::toast(variant: 'success', text: __('Nutrient created.'));

        $this->redirectRoute('nutrients.index', navigate: true);
    }
}
