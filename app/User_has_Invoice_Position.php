<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_has_Invoice_Position extends Model
{
    protected $fillable = ['user_id', 'comment', 'invoice_position_id', 'amount'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function invoice_position(){
        return $this->belongsTo(Invoice_Position::class);
    }
}
