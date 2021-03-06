<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class emailCc extends Model
{
    protected $table = 'email_cc'; 
    public $timestamps = false;

    protected $fillable = [
        'email', 'notice', 'user_id'
    ];

    public function User(){
        return $this->belongsTo(User::class);
    }
}
