<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define(
            'manage-users',
            fn (User $user): bool => $user->role === UserRole::Administrador,
        );

        Gate::define(
            'manage-academic-data',
            fn (User $user): bool => $user->role === UserRole::Administrador,
        );

        Gate::define(
            'manage-matriculas',
            fn (User $user): bool => in_array($user->role, [UserRole::Administrador, UserRole::Funcionario], true),
        );

        Gate::define(
            'manage-professors',
            fn (User $user): bool => in_array($user->role, [UserRole::Administrador, UserRole::Funcionario], true),
        );
    }
}
