<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments'; 
    public $timestamps = false;

    protected $fillable = [
        'value', 'date', 'prescribing_id', 'bank_payment_id'
    ];

    public function prescribing(){
        return $this->belongsTo(Prescribing::class);
    }

    public function bank_payment(){
        return $this->belongsTo(Bank_Payment::class);
    }
}
