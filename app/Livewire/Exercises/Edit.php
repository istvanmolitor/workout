<?php

namespace App\Livewire\Exercises;

use App\Models\Exercise;
use App\Models\ExerciseCategory;
use App\Models\ExerciseType;
use App\Repositories\Contracts\ExerciseCategoryRepositoryInterface;
use App\Repositories\Contracts\ExerciseRepositoryInterface;
use App\Repositories\Contracts\ExerciseTypeRepositoryInterface;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gyakorlat szerkesztése')]
class Edit extends Component
{
    protected ExerciseRepositoryInterface $exerciseRepository;

    protected ExerciseCategoryRepositoryInterface $exerciseCategoryRepository;

    protected ExerciseTypeRepositoryInterface $exerciseTypeRepository;

    #[Locked]
    public Exercise $exercise;

    public string $name = '';

    public ?int $category_id = null;

    public ?int $exercise_type_id = null;

    public function boot(
        ExerciseRepositoryInterface $exerciseRepository,
        ExerciseCategoryRepositoryInterface $exerciseCategoryRepository,
        ExerciseTypeRepositoryInterface $exerciseTypeRepository,
    ): void {
        $this->exerciseRepository = $exerciseRepository;
        $this->exerciseCategoryRepository = $exerciseCategoryRepository;
        $this->exerciseTypeRepository = $exerciseTypeRepository;
    }

    /**
     * Mount the component.
     */
    public function mount(Exercise $exercise): void
    {
        $this->exercise = $exercise;
        $this->name = $exercise->name;
        $this->category_id = $exercise->category_id;
        $this->exercise_type_id = $exercise->exercise_type_id;
    }

    /**
     * Get the categories available to choose from.
     *
     * @return Collection<int, ExerciseCategory>
     */
    #[Computed]
    public function categories(): Collection
    {
        return $this->exerciseCategoryRepository->all();
    }

    /**
     * Get the exercise types available to choose from.
     *
     * @return Collection<int, ExerciseType>
     */
    #[Computed]
    public function exerciseTypes(): Collection
    {
        return $this->exerciseTypeRepository->all();
    }

    /**
     * Update the exercise.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('exercises', 'name')->ignore($this->exercise->id)],
            'category_id' => ['required', 'integer', 'exists:exercise_categories,id'],
            'exercise_type_id' => ['required', 'integer', 'exists:exercise_types,id'],
        ]);

        $this->exerciseRepository->update($this->exercise, $validated);

        Flux::toast(variant: 'success', text: __('Exercise updated.'));

        $this->redirectRoute('exercises.index', navigate: true);
    }
}
