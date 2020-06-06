<?php

namespace NickWhitt\Gridcoin;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $dates = ['time'];
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public static function storeClientResponse($result)
    {
        return static::updateOrCreate(['hash' => $result->hash], [
            'height' => $result->height,
            'mint' => $result->mint,
            'time' => $result->time,
            'difficulty' => $result->difficulty,
            'cpid' => $result->CPID,
            'interest' => $result->Interest,
            'researchsubsidy' => $result->ResearchSubsidy,
            'previousblockhash' => $result->previousblockhash ?? null,
            'nextblockhash' => $result->nextblockhash ?? null,
            'lastporblockhash' => $result->LastPORBlockHash ?: null,
        ]);
    }
}
