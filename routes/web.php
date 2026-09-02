<?php

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
});

require __DIR__.'/settings.php';
