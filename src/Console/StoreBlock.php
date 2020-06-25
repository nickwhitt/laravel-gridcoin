<?php

namespace NickWhitt\Gridcoin\Console;

use Illuminate\Console\Command;
use NickWhitt\Gridcoin\Jobs\StoreBlock as StoreBlockJob;
use NickWhitt\Gridcoin\RpcClient;

class StoreBlock extends Command
{
    protected $description = 'Stores block data for aggregate calculations';
    protected $signature = 'gridcoin:store-block {hash?} {--no-follow}';

    public function handle(RpcClient $client)
    {
        $hash = $this->argument('hash') ?: $client->getBestBlockHash()->result;
        if (is_null($hash)) {
            return $this->error('Hash required');
        }

        dispatch(new StoreBlockJob($hash));
    }
}
