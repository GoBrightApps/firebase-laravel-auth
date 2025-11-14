<?php

declare(strict_types=1);

namespace Bright\Fauth;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Kreait\Laravel\Firebase\Facades\Firebase;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('fauth', fn (): Fauth => new Fauth(Firebase::auth()));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Auth::provider('fauth', function (Application $app, array $config) {
            /** @var \Illuminate\Contracts\Hashing\Hasher $hasher */
            $hasher = $app->make('hash');

            return new AuthUserProvider($hasher, (string) $config['model']);
        });
    }
}
