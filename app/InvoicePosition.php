<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoicePosition extends Model
{
    protected $fillable = ['name', 'invoice_id', 'paid_by_teacher', 'total_amount', 'document_number'];

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public function userHasInvoicePosition(){
        return $this->hasMany(UserHasInvoicePosition::class, 'invoice_position_id');
    }
}
