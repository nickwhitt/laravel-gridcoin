<?php

namespace NickWhitt\Gridcoin;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use NickWhitt\Gridcoin\Console\StoreBlock;

class ServiceProvider extends LaravelServiceProvider implements DeferrableProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/gridcoin.php' => config_path('gridcoin.php'),
        ]);

        $this->mergeConfigFrom(__DIR__.'/../config/gridcoin.php', 'gridcoin');
        $this->loadMigrationsFrom(__DIR__.'/../migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                StoreBlock::class,
            ]);
        }
    }

    public function register()
    {
        $this->app->singleton(RpcClient::class, function ($app) {
            return new RpcClient([
                'base_uri' => config('gridcoin.server_url'),
                'auth' => [config('gridcoin.rpc_user'), config('gridcoin.rpc_pass')],
            ]);
        });
    }

    public function provides()
    {
        return [RpcClient::class];
    }
}
