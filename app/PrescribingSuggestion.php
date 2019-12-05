<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FosUser;
use App\UserHasPrescribingSuggestion;

class PrescribingSuggestion extends Model
{
    protected $fillable = ['due_until', 'reason_id', 'reason_suggestion', 'finished', 'approved', 'title', 'description', 'author_id'];

    public function author(){
        return $this->belongsTo(FosUser::class, 'author_id', 'id');
    }

    public function positions(){
        return $this->hasMany(UserHasPrescribingSuggestion::class);
    }
}
