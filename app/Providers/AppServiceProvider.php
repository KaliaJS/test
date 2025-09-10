<?php

namespace App\Providers;

use App\Enums\UserRole;
use DeviceDetector\Cache\LaravelCache;
use DeviceDetector\DeviceDetector as DD;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        JsonResource::withoutWrapping();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme('https'); 
        Model::preventLazyLoading(!app()->environment('production'));
        Model::unguard();
        Validator::excludeUnvalidatedArrayKeys();

        Gate::define('admin', fn ($user) => $user->role === UserRole::ADMIN);
        Gate::define('terminal', fn ($user) => $user->role === UserRole::TERMINAL);
        Gate::define('employee', fn ($user) => $user->role === UserRole::EMPLOYEE);

        $this->app->bind(DeviceDetector::class, function ($app) {
            $userAgent = $app['request']->userAgent() ?? '';
            
            $detector = new DD($userAgent);
            $detector->setCache(new LaravelCache());
            $detector->parse();
            
            return new DeviceDetector($detector);
        });

        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }
}
