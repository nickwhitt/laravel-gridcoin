<?php

namespace NickWhitt\Gridcoin;

use Illuminate\Database\Eloquent\Model;

class TxOut extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'txid', 'txid');
    }
}
