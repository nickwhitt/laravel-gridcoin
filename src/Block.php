<?php

namespace NickWhitt\Gridcoin;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $dates = ['time'];
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'blockhash', 'hash');
    }

    public static function storeClientResponse($result)
    {
        return static::updateOrCreate(['hash' => $result->hash], [
            'height' => $result->height,
            'mint' => $result->mint,
            'moneysupply' => $result->MoneySupply,
            'time' => $result->time,
            'difficulty' => $result->difficulty,
            'cpid' => $result->CPID,
            'grcaddress' => $result->GRCAddress,
            'interest' => $result->Interest,
            'researchsubsidy' => $result->ResearchSubsidy,
            'previousblockhash' => $result->previousblockhash ?? null,
            'nextblockhash' => $result->nextblockhash ?? null,
            'lastporblockhash' => $result->LastPORBlockHash ?: null,
        ]);
    }
}
