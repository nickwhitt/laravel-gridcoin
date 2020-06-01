<?php

namespace NickWhitt\Gridcoin\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use NickWhitt\Gridcoin\Block;
use NickWhitt\Gridcoin\RpcClient;

class FollowBlock implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $block;

    public function __construct(Block $block)
    {
        $this->block = $block;
    }

    public function handle()
    {
        collect([
            $this->block->previousblockhash,
            $this->block->nextblockhash,
            $this->block->lastporblockhash
        ])->each(
            fn($hash) => $this->storeAndFollow($hash)
        );
    }

    protected function storeAndFollow($hash)
    {
        if (is_null($hash)) {
            return;
        }

        if (Block::where('hash', $hash)->exists()) {
            return;
        }

        $response = resolve(RpcClient::class)->getblock($hash);
        if (is_null($response->result)) {
            Log::error("Server error for $hash: $response->error");
            return;
        }

        $block = Block::storeClientResponse($response->result);
        Log::info("New block stored $hash");

        dispatch(new static($block));
    }
}
