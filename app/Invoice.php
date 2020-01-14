<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices'; 
    protected $fillable = ['reason', 'author_id', 'annotation', 'approved', 'saved', 'total_amount', 'date'];

    public function author(){
        return $this->belongsTo(FosUser::class, 'author_id');
    }

    public function positions(){
        return $this->hasMany(InvoicePosition::class, 'invoice_id');
    }
}
