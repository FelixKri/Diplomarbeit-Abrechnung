<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserHasInvoicePosition extends Model
{
    protected $fillable = ['user_id', 'comment', 'invoice_position_id', 'amount'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function invoicePosition(){
        return $this->belongsTo(InvoicePosition::class);
    }
}
