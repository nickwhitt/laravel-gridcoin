<?php

namespace NickWhitt\Gridcoin\Console;

use Illuminate\Console\Command;
use NickWhitt\Gridcoin\Block;
use NickWhitt\Gridcoin\Jobs\FollowBlock;
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

        $response = $client->getblock($hash);
        if (!is_null($response->error)) {
            return $this->error($response->error->message);
        }

        $block = Block::storeClientResponse($response->result);

        if ($this->option('no-follow')) {
            return;
        }

        dispatch(new FollowBlock($block));
    }
}
