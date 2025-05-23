<?php

namespace App\Providers;

use App\Modules\Company\Domain\Models\Company;
use App\Modules\Company\Domain\Policies\CompanyPolicy;
use App\Modules\User\Domain\Models\User;
use App\Modules\User\Domain\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(\App\Modules\Core\Domain\Providers\CoreServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Company::class, CompanyPolicy::class);
    }
}
