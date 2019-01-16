<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prescribing extends Model
{
    protected $table = 'persons'; 
    public $timestamps = false;

    protected $fillable = [
        'title', 'value', 'user_id', 'due_until', 'created_at', 'reason_id', 'finished', 'kontoauszug_id'
    ];

    public function reason(){
        return $this->hasOne(Reason::class);
    }

    public function payments(){
        return $this->belongsToMany(Payments::class);
    }

    public function author(){
        return $this->belongsTo(Tos_User::class, 'user_id');
    }
}
