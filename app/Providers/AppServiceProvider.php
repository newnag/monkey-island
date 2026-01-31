<?php

namespace App\Providers;

use App\Models\Subject;
use Illuminate\Support\Facades\Cache;
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
        // Cache active subjects globally (ใช้บ่อยมาก)
        $this->app->singleton('cached.subjects.active', function () {
            return Cache::remember('subjects.active', 3600, function () {
                return Subject::active()->withCount('questions')->get();
            });
        });
    }
}
