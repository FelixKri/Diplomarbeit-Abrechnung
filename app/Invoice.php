<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices'; 
    protected $fillable = ['reason_id', 'author_id', 'annotation', 'approved', 'saved', 'total_amount', 'date', 'due_until', 'imported_prescribing'];

    public function author(){
        return $this->belongsTo(FosUser::class, 'author_id');
    }

    public function positions(){
        return $this->hasMany(InvoicePosition::class, 'invoice_id');
    }

    public function reason(){
        return $this->belongsTo(Reason::class, 'reason_id');
    }
}
