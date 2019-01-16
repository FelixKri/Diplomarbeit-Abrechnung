<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    //How to work with models without migrations
    protected $table = 'persons'; 
    public $timestamps = false;

    protected $fillable = [
        'firstname', 'lastname', 'gender',
    ];
}
