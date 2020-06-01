<?php

namespace NickWhitt\Gridcoin;

use Illuminate\Support\ServiceProvider;
use NickWhitt\Gridcoin\Console\StoreBlock;

class LumenServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->configure('gridcoin');

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
}
