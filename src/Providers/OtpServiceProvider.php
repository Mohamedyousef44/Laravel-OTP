<?php

namespace Ganagon\Otp\Providers;

use Illuminate\Support\ServiceProvider;
use Ganagon\Otp\Services\OtpService;
use Ganagon\Otp\Contracts\OtpRepositoryInterface;
use Ganagon\Otp\Repositories\CacheRepository;

class OtpServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/otp.php', 'otp');

        $this->app->bind(OtpRepositoryInterface::class, CacheRepository::class);
        $this->app->singleton(OtpService::class, fn() => new OtpService(config('otp')));
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/otp.php' => config_path('otp.php')
        ], 'config');
    }
}
