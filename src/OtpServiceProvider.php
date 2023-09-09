<?php

namespace SadiqSalau\LaravelOtp;

use Illuminate\Support\ServiceProvider;
use SadiqSalau\LaravelOtp\OtpMakeCommand;

class OtpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/otp.php',
            'otp'
        );

        $this->app->singleton('otp', function ($app) {
            return new OtpBroker(
                $app->make($app['config']['otp.store'])
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/otp.php' => config_path('otp.php'),
        ], 'otp');

        if ($this->app->runningInConsole()) {
            $this->commands([
                OtpMakeCommand::class,
            ]);
        }
    }
}
