<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;
use App\Repository\RepositoryInterface;
use App\Repository\FilmRepositoryInterface;
use App\Repository\CriticRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;
use App\Repository\Eloquent\FilmRepository;
use App\Repository\Eloquent\CriticRepository;
use App\Repository\Eloquent\UserRepository;
use App\Models\Film;
use APp\Models\Critic;
use APp\Models\User;

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
        $this->app->singleton(CriticRepositoryInterface::class, function (Application $app) {
            return new CriticRepository(new Critic());
        });
        $this->app->singleton(UserRepositoryInterface::class, function (Application $app) {
            return new UserRepository(new User());
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
