<?php

namespace App\Livewire\Fields;

use App\Models\Field;
use App\Repositories\Contracts\FieldRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Mezők')]
class Manage extends Component
{
    protected FieldRepositoryInterface $fieldRepository;

    public function boot(FieldRepositoryInterface $fieldRepository): void
    {
        $this->fieldRepository = $fieldRepository;
    }

    /**
     * Get all fields in the catalog.
     *
     * @return Collection<int, Field>
     */
    #[Computed]
    public function fields(): Collection
    {
        return $this->fieldRepository->all();
    }

    /**
     * Delete a field from the catalog.
     */
    public function delete(Field $field): void
    {
        try {
            $this->fieldRepository->delete($field);
        } catch (QueryException) {
            Flux::toast(variant: 'danger', text: __('This field has recorded values and cannot be deleted.'));

            return;
        }

        unset($this->fields);

        Flux::toast(variant: 'success', text: __('Field deleted.'));
    }
}
