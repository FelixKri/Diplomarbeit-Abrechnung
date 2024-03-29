<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    protected $table = 'reasons'; 
    public $timestamps = false;

    protected $fillable = [
        'title', 'account_bookkeeping', 'active', 'geschlossen', 'saptext', 'sapzuordnung'
    ];

    public function prescribings(){
        return $this->hasMany(PrescribingSuggestion::class, 'reason_id');
    }
}
