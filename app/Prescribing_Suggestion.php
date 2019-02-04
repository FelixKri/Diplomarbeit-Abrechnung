<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prescribing_Suggestion extends Model
{
    protected $fillable = ['due_until', 'reason_id', 'reason_suggestion', 'finished', 'approved', 'title', 'description', 'author_id'];

    public function author(){
        return $this->belongsTo(User::class, 'author_id');
    }
}
