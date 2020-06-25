<?php

namespace NickWhitt\Gridcoin\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use NickWhitt\Gridcoin\Block;
use NickWhitt\Gridcoin\RpcClient;

class StoreBlock implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $hash;

    public function __construct($hash)
    {
        $this->hash = $hash;
    }

    public function handle()
    {
        $response = app(RpcClient::class)->getblock($this->hash);
        if (is_null($response->result)) {
            Log::error("Server error for $this->hash: {$response->error->message}");
            return;
        }

        $block = Block::storeClientResponse($response->result);
        if ($block->wasRecentlyCreated || $block->wasChanged()) {
            Log::info("Stored block $this->hash");

            collect($response->result->tx)->each(
                fn($tx) => dispatch(new StoreTransaction($tx))
            );

            dispatch(new FollowBlock($block));
        }
    }
}
