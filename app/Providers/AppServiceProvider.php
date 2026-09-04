<?php

namespace App\Providers;

use App\Repositories\BodyWeightRepository;
use App\Repositories\Contracts\BodyWeightRepositoryInterface;
use App\Repositories\Contracts\ExerciseCategoryRepositoryInterface;
use App\Repositories\Contracts\ExerciseRepositoryInterface;
use App\Repositories\Contracts\ExerciseTypeRepositoryInterface;
use App\Repositories\Contracts\FieldRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\WorkoutPlanRepositoryInterface;
use App\Repositories\Contracts\WorkoutRepositoryInterface;
use App\Repositories\ExerciseCategoryRepository;
use App\Repositories\ExerciseRepository;
use App\Repositories\ExerciseTypeRepository;
use App\Repositories\FieldRepository;
use App\Repositories\UserRepository;
use App\Repositories\WorkoutPlanRepository;
use App\Repositories\WorkoutRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BodyWeightRepositoryInterface::class, BodyWeightRepository::class);
        $this->app->bind(ExerciseCategoryRepositoryInterface::class, ExerciseCategoryRepository::class);
        $this->app->bind(ExerciseRepositoryInterface::class, ExerciseRepository::class);
        $this->app->bind(ExerciseTypeRepositoryInterface::class, ExerciseTypeRepository::class);
        $this->app->bind(FieldRepositoryInterface::class, FieldRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(WorkoutPlanRepositoryInterface::class, WorkoutPlanRepository::class);
        $this->app->bind(WorkoutRepositoryInterface::class, WorkoutRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
