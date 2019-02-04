<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_has_Prescribing_Suggestion extends Model
{
    protected $fillable = ['user_id', 'amount', 'annotation', 'precribing_suggestion_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function prescribing_suggestion(){
        return $this->belongsTo(Prescribing_Suggestion::class);
    }
}
