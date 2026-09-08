<?php

namespace App\Concerns;

use App\Models\Food;
use App\Models\Nutrient;
use App\Repositories\Contracts\NutrientRepositoryInterface;
use App\Repositories\Contracts\NutritionLookupServiceInterface;
use Flux\Flux;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;

/**
 * Shared barcode and dynamic nutrient-catalog fields/behaviour for the food Create and Edit
 * Livewire components. Nutrient values are keyed by the Nutrient catalog's slug.
 */
trait HasNutritionFields
{
    protected NutrientRepositoryInterface $nutrientRepository;

    public string $barcode = '';

    /** @var array<string, string> nutrient slug => string value */
    public array $nutrientValues = [];

    public bool $nutritionSynced = false;

    /**
     * Get the nutrient catalog to render as form fields, in display order.
     *
     * @return Collection<int, Nutrient>
     */
    #[Computed]
    public function nutrients(): Collection
    {
        return $this->nutrientRepository->all();
    }

    /**
     * Get the validation rules for the barcode field.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function barcodeRules(?int $ignoreId = null): array
    {
        return [
            'nullable',
            'string',
            'max:64',
            Rule::unique('foods', 'barcode')->ignore($ignoreId),
        ];
    }

    /**
     * Get the validation rules for the dynamic nutrient value fields.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function nutrientValueRules(): array
    {
        return $this->nutrients
            ->mapWithKeys(fn (Nutrient $nutrient) => [
                "nutrientValues.{$nutrient->slug}" => ['nullable', 'numeric', 'min:0', 'max:100000'],
            ])
            ->all();
    }

    /**
     * Fill the barcode and nutrient value fields from an existing food record.
     */
    protected function fillNutritionFields(Food $food): void
    {
        $this->barcode = (string) $food->barcode;
        $this->nutritionSynced = (bool) $food->nutrition_synced;

        $existingValues = $food->nutrients->pluck('pivot.value', 'slug');

        $this->nutrientValues = $this->nutrients
            ->mapWithKeys(fn (Nutrient $nutrient) => [
                $nutrient->slug => $existingValues->has($nutrient->slug)
                    ? number_format((float) $existingValues->get($nutrient->slug), $nutrient->decimal_places, '.', '')
                    : '',
            ])
            ->all();
    }

    /**
     * Build the pivot sync array (nutrient_id => value) from the current form input.
     *
     * @return array<int, array{value: float}>
     */
    protected function nutrientValuesToSync(): array
    {
        return $this->nutrients
            ->filter(fn (Nutrient $nutrient) => filled($this->nutrientValues[$nutrient->slug] ?? null))
            ->mapWithKeys(fn (Nutrient $nutrient) => [
                $nutrient->id => ['value' => (float) $this->nutrientValues[$nutrient->slug]],
            ])
            ->all();
    }

    /**
     * Look up nutrition data by barcode (if given) or by name, and fill the form fields with the result.
     */
    public function fetchNutrition(NutritionLookupServiceInterface $nutritionLookupService): void
    {
        $result = filled($this->barcode)
            ? $nutritionLookupService->lookupByBarcode($this->barcode)
            : (filled($this->name) ? $nutritionLookupService->lookupByName($this->name) : null);

        if ($result === null) {
            Flux::toast(variant: 'danger', text: __('No nutrition data was found for this food.'));

            return;
        }

        if ($result['calories'] !== null) {
            $this->calories = (string) $result['calories'];
        }

        foreach ($this->nutrients as $nutrient) {
            if (($result[$nutrient->slug] ?? null) !== null) {
                $this->nutrientValues[$nutrient->slug] = (string) $result[$nutrient->slug];
            }
        }

        $this->nutritionSynced = true;

        Flux::toast(variant: 'success', text: __('Nutrition data filled in from the free food database.'));
    }
}
