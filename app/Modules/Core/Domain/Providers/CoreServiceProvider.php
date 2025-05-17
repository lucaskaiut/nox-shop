<?php

namespace App\Modules\Core\Domain\Providers;

use App\Modules\Company\Domain\Singletons\Company;
use Illuminate\Support\ServiceProvider;
use App\Modules\Core\Domain\Commands\MakeServiceCommand;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            MakeServiceCommand::class,
        ]);

        $this->app->singleton('company', function ($app) {
            return new Company();
        });
    }

    public function boot(): void
    {
        //
    }
}
