<?php

declare(strict_types=1);

namespace Bright\Fauth;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Kreait\Laravel\Firebase\Facades\Firebase;

class ServiceProvider extends BaseServiceProvider implements DeferrableProvider
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

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return ['fauth'];
    }
}
