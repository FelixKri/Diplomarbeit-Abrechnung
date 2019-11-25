<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['reason_id', 'author_id', 'annotation', 'approved', 'saved', 'total_amount', 'date', 'iban'];

    public function author(){
        return $this->belongsTo(User::class, 'author_id');
    }
}
