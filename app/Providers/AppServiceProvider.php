<?php

namespace App\Providers;

use App\KongClient\Contracts\OAuthLinkInterface;
use App\KongClient\OAuthLink;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        OAuthLinkInterface::class => OAuthLink::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ClientInterface::class, function ($app) {
            return new Client(['http_errors'=>false]);
        });
    }
}
