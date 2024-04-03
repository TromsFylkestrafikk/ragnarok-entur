<?php

namespace Ragnarok\Entur;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Ragnarok\Entur\Sinks\SinkEnturRoutes;
use Ragnarok\Entur\Sinks\SinkEnturSales;
use Ragnarok\Entur\Sinks\SinkEnturStops;
use Ragnarok\Entur\Services\EnturAuthToken;
use Ragnarok\Sink\Facades\SinkRegistrar;

class EnturServiceProvider extends ServiceProvider
{
    public $singletons = [
        EnturAuthToken::class => EnturAuthToken::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ragnarok_entur.php', 'ragnarok_entur');
        $this->publishConfig();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        SinkRegistrar::register(SinkEnturRoutes::class);
        SinkRegistrar::register(SinkEnturStops::class);
        SinkRegistrar::register(SinkEnturSales::class);
    }

    /**
     * Publish Config
     *
     * @return void
     */
    public function publishConfig(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ragnarok_entur.php' => config_path('ragnarok_entur.php'),
            ], ['config', 'config-entur', 'entur']);
        }
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    /**
    * Get route group configuration array.
    *
    * @return array
    */
    protected function routeConfiguration(): array
    {
        return [
            'namespace'  => "Ragnarok\Entur\Http\Controllers",
            'middleware' => 'api',
            'prefix'     => 'api'
        ];
    }
}
