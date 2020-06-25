<?php

namespace NickWhitt\Gridcoin\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use NickWhitt\Gridcoin\Block;

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
        ])->filter()->each(
            fn($hash) => dispatch(new StoreBlock($hash))
        );
    }
}
