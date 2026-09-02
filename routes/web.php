<?php

use App\Livewire\ExerciseCategories\Create as CreateExerciseCategory;
use App\Livewire\ExerciseCategories\Edit as EditExerciseCategory;
use App\Livewire\ExerciseCategories\Manage as ManageExerciseCategories;
use App\Livewire\Exercises\Create as CreateExercise;
use App\Livewire\Exercises\Edit as EditExercise;
use App\Livewire\Exercises\Manage as ManageExercises;
use App\Livewire\WorkoutPlans\Create as CreateWorkoutPlan;
use App\Livewire\WorkoutPlans\Edit as EditWorkoutPlan;
use App\Livewire\WorkoutPlans\Manage as ManageWorkoutPlans;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('workout-plans', ManageWorkoutPlans::class)->name('workout-plans.index');
    Route::livewire('workout-plans/create', CreateWorkoutPlan::class)->name('workout-plans.create');
    Route::livewire('workout-plans/{workoutPlan}/edit', EditWorkoutPlan::class)->name('workout-plans.edit');

    Route::livewire('exercises', ManageExercises::class)->name('exercises.index');
    Route::livewire('exercises/create', CreateExercise::class)->name('exercises.create');
    Route::livewire('exercises/{exercise}/edit', EditExercise::class)->name('exercises.edit');

    Route::livewire('exercise-categories', ManageExerciseCategories::class)->name('exercise-categories.index');
    Route::livewire('exercise-categories/create', CreateExerciseCategory::class)->name('exercise-categories.create');
    Route::livewire('exercise-categories/{exerciseCategory}/edit', EditExerciseCategory::class)->name('exercise-categories.edit');
});

require __DIR__.'/settings.php';
