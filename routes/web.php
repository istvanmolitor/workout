<?php

use App\Livewire\BodyWeights\Create as CreateBodyWeight;
use App\Livewire\BodyWeights\Edit as EditBodyWeight;
use App\Livewire\BodyWeights\Manage as ManageBodyWeights;
use App\Livewire\Dashboard;
use App\Livewire\ExerciseCategories\Create as CreateExerciseCategory;
use App\Livewire\ExerciseCategories\Edit as EditExerciseCategory;
use App\Livewire\ExerciseCategories\Manage as ManageExerciseCategories;
use App\Livewire\Exercises\Create as CreateExercise;
use App\Livewire\Exercises\Edit as EditExercise;
use App\Livewire\Exercises\Manage as ManageExercises;
use App\Livewire\ExerciseTypes\Create as CreateExerciseType;
use App\Livewire\ExerciseTypes\Edit as EditExerciseType;
use App\Livewire\ExerciseTypes\Manage as ManageExerciseTypes;
use App\Livewire\Fields\Create as CreateField;
use App\Livewire\Fields\Edit as EditField;
use App\Livewire\Fields\Manage as ManageFields;
use App\Livewire\WorkoutPlans\Create as CreateWorkoutPlan;
use App\Livewire\WorkoutPlans\Edit as EditWorkoutPlan;
use App\Livewire\WorkoutPlans\Manage as ManageWorkoutPlans;
use App\Livewire\Workouts\Calendar as WorkoutCalendar;
use App\Livewire\Workouts\Edit as EditWorkout;
use App\Livewire\Workouts\Manage as ManageWorkouts;
use App\Livewire\Workouts\Perform as PerformWorkout;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');

    Route::livewire('body-weights', ManageBodyWeights::class)->name('body-weights.index');
    Route::livewire('body-weights/create', CreateBodyWeight::class)->name('body-weights.create');
    Route::livewire('body-weights/{bodyWeight}/edit', EditBodyWeight::class)->name('body-weights.edit');

    Route::livewire('workout-plans', ManageWorkoutPlans::class)->name('workout-plans.index');
    Route::livewire('workout-plans/create', CreateWorkoutPlan::class)->name('workout-plans.create');
    Route::livewire('workout-plans/{workoutPlan}/edit', EditWorkoutPlan::class)->name('workout-plans.edit');

    Route::livewire('workouts', ManageWorkouts::class)->name('workouts.index');
    Route::livewire('workouts/calendar', WorkoutCalendar::class)->name('workouts.calendar');
    Route::livewire('workouts/{workout}/edit', EditWorkout::class)->name('workouts.edit');
    Route::livewire('workouts/{workout}/perform', PerformWorkout::class)->name('workouts.perform');

    Route::livewire('exercises', ManageExercises::class)->name('exercises.index');
    Route::livewire('exercises/create', CreateExercise::class)->name('exercises.create');
    Route::livewire('exercises/{exercise}/edit', EditExercise::class)->name('exercises.edit');

    Route::livewire('exercise-categories', ManageExerciseCategories::class)->name('exercise-categories.index');
    Route::livewire('exercise-categories/create', CreateExerciseCategory::class)->name('exercise-categories.create');
    Route::livewire('exercise-categories/{exerciseCategory}/edit', EditExerciseCategory::class)->name('exercise-categories.edit');

    Route::livewire('exercise-types', ManageExerciseTypes::class)->name('exercise-types.index');
    Route::livewire('exercise-types/create', CreateExerciseType::class)->name('exercise-types.create');
    Route::livewire('exercise-types/{exerciseType}/edit', EditExerciseType::class)->name('exercise-types.edit');

    Route::livewire('fields', ManageFields::class)->name('fields.index');
    Route::livewire('fields/create', CreateField::class)->name('fields.create');
    Route::livewire('fields/{field}/edit', EditField::class)->name('fields.edit');
});

require __DIR__.'/settings.php';
