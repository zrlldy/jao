<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('public-job-hirings', function (Request $request): Limit {
            return Limit::perMinute(60)->by('public-jobs:ip:'.$request->ip());
        });

        RateLimiter::for('public-application-form', function (Request $request): array {
            return [
                Limit::perMinute(30)->by('public-form:ip:'.$request->ip()),
                Limit::perMinute(15)->by('public-form:token:'.hash('sha256', (string) $request->route('token'))),
            ];
        });

        RateLimiter::for('public-application-submission', function (Request $request): array {
            return [
                Limit::perMinute(10)->by('public-submission:ip:'.$request->ip()),
                Limit::perMinute(5)->by('public-submission:token:'.hash('sha256', (string) $request->route('token'))),
            ];
        });

        RateLimiter::for('private-invitation-create', function (Request $request): Limit {
            return Limit::perMinute(10)->by('private-invite:ip:'.$request->ip());
        });
    }
}
