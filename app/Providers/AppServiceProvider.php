<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\CustomRoute;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Register dynamic routes
        $this->registerDynamicRoutes();

        // Share common data with all views
        view()->composer('*', function ($view) {
            $view->with('settings', \App\Models\Setting::all()->pluck('value', 'key'));
        });
    }

    protected function registerDynamicRoutes()
    {
        try {
            $routes = CustomRoute::active()->ordered()->get();
            
            foreach ($routes as $route) {
                if ($route->isValidController()) {
                    Route::middleware($route->middleware_array)
                        ->{$route->method}($route->path, [$route->controller, $route->method])
                        ->name("custom.{$route->name}");
                }
            }
        } catch (\Exception $e) {
            // Log error but don't break the application
            \Log::error('Failed to load dynamic routes: ' . $e->getMessage());
        }
    }
}