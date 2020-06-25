<?php

namespace NickWhitt\Gridcoin;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $dates = ['time'];
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function block()
    {
        return $this->belongsTo(Block::class, 'hash', 'blockhash');
    }

    public function outputs()
    {
        return $this->hasMany(TxOut::class, 'txid', 'txid');
    }

    public function inputs()
    {
        return $this->hasMany(TxOut::class, 'input', 'txid');
    }

    public static function storeClientResponse($result)
    {
        return static::updateOrCreate(['txid' => $result->txid], [
            'blockhash' => $result->blockhash,
            'time' => $result->time,
        ]);
    }
}
