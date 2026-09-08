<?php

namespace App\Livewire\Nutrients;

use App\Models\Nutrient;
use App\Repositories\Contracts\NutrientRepositoryInterface;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tápanyag szerkesztése')]
class Edit extends Component
{
    protected NutrientRepositoryInterface $nutrientRepository;

    #[Locked]
    public Nutrient $nutrient;

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
     * Mount the component.
     */
    public function mount(Nutrient $nutrient): void
    {
        $this->nutrient = $nutrient;
        $this->name = $nutrient->name;
        $this->slug = $nutrient->slug;
        $this->unit = $nutrient->unit;
        $this->decimal_places = (string) $nutrient->decimal_places;
        $this->sort_order = (string) $nutrient->sort_order;
    }

    /**
     * Update the nutrient.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('nutrients', 'name')->ignore($this->nutrient->id)],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', Rule::unique('nutrients', 'slug')->ignore($this->nutrient->id)],
            'unit' => ['required', 'string', 'max:20'],
            'decimal_places' => ['required', 'integer', 'min:0', 'max:6'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $this->nutrientRepository->update($this->nutrient, [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'unit' => $validated['unit'],
            'decimal_places' => (int) $validated['decimal_places'],
            'sort_order' => (int) $validated['sort_order'],
        ]);

        Flux::toast(variant: 'success', text: __('Nutrient updated.'));

        $this->redirectRoute('nutrients.index', navigate: true);
    }
}
