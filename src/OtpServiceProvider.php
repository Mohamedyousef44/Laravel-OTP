<?php

namespace Yossivic\Otp;

use Illuminate\Support\ServiceProvider;
use Yossivic\Otp\Services\OtpService;
use Yossivic\Otp\Contracts\OtpRepositoryInterface;
use Yossivic\Otp\Repositories\CacheRepository;
use Yossivic\Otp\Repositories\DatabaseRepository;

class OtpServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/config/otp.php', 'otp');

        // Decide repository dynamically
        $this->app->bind(OtpRepositoryInterface::class, function ($app) {
            $repository = config('otp.repository');

            return match ($repository) {
                'db' => new DatabaseRepository(),
                'cache' => new CacheRepository(),
                default => throw new \InvalidArgumentException(
                    "Invalid OTP repository '{$repository}' configured in otp.php. Allowed: 'cache' or 'db'."
                ),
            };
        });
        $this->app->singleton(OtpService::class, fn() => new OtpService(config('otp')));
        $this->app->singleton('otp', fn($app) => new OtpService(config('otp')));
    }

    public function boot()
    {
        $this->publishes([
            dirname(__DIR__) . '/config/otp.php' => config_path('otp.php')
        ], 'otp-config');

        // Publish migrations only if the DB repository is used
        if (config('otp.repository') === 'db') {
            $this->loadMigrationsFrom(dirname(__DIR__) . '/src/database/migrations');

            $this->publishes([
                dirname(__DIR__) . '/src/database/migrations/' => database_path('migrations')
            ], 'otp-migrations');
        }
    }
}
