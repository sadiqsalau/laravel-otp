<?php

namespace SadiqSalau\LaravelOtp;

use Illuminate\Support\ServiceProvider;
use SadiqSalau\LaravelOtp\OtpMakeCommand;
use SadiqSalau\LaravelOtp\OtpBroker;

class OtpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        /** Merge configurations */
        if (!app()->configurationIsCached()) {
            $this->mergeConfigFrom(
                __DIR__ . '/../config/otp.php',
                'otp'
            );
        }

        /** Bind otp broker */
        app()->bind('otp', OtpBroker::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (app()->runningInConsole()) {

            /** Publish config */
            $this->publishes([
                __DIR__ . '/../config/otp.php' => config_path('otp.php'),
            ], 'otp');

            /** Register otp make command */
            $this->commands([
                OtpMakeCommand::class,
            ]);
        }
    }
}
