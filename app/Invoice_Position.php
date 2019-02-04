<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice_Position extends Model
{
    protected $fillable = ['designation', 'invoice_id', 'paid_by_teacher', 'total_amount', 'document_number'];

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public function user_has_invoice_position(){
        return $this->hasMany(User_has_Invoice_Position::class);
    }
}
