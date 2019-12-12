<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankPayment extends Model
{
    protected $table = 'bank_payments'; 
    public $timestamps = false;

    protected $fillable = [
        'number', 'value', 'date', 'zuordnungsdatenKtoAZ'
    ];

    public function payments(){
        return $this->belongsTo(Payment::class);
    }
}
