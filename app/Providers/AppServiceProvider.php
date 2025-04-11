<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;
use App\Repository\RepositoryInterface;
use App\Repository\FilmRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;
use App\Repository\Eloquent\FilmRepository;
use App\Models\Film;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RepositoryInterface::class, BaseRepository::class);
        $this->app->singleton(FilmRepositoryInterface::class, function (Application $app) {
            return new FilmRepository(new Film());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
