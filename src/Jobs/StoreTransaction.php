<?php

namespace NickWhitt\Gridcoin\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use NickWhitt\Gridcoin\Transaction;
use NickWhitt\Gridcoin\TxOut;
use NickWhitt\Gridcoin\RpcClient;

class StoreTransaction implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $txid;

    public function __construct($txid)
    {
        $this->txid = $txid;
    }

    public function handle()
    {
        $response = app(RpcClient::class)->getTransaction($this->txid);
        if (is_null($response->result)) {
            Log::error("Server error for $this->txid: {$response->error->message}");
            return;
        }

        $tx = Transaction::storeClientResponse($response->result);
        if ($tx->wasRecentlyCreated || $tx->wasChanged()) {
            Log::info("Stored transaction $this->txid");

            collect($response->result->vout)
                ->filter(fn($out) => $out->value > 0)
                ->each(fn($out) => $tx->outputs()->updateOrCreate(
                    ['index' => $out->n],
                    ['value' => $out->value, 'address' => $out->scriptPubKey->addresses[0] ?? null]
                ));

            collect($response->result->vin)
                ->filter(fn($in) => $in->txid ?? false)
                ->each(fn($in) => TxOut::updateOrCreate(
                    ['txid' => $in->txid, 'index' => $in->vout],
                    ['input' => $tx->txid]
                ));
        }
    }
}
