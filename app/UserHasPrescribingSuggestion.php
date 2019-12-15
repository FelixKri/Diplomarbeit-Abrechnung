<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FosUser;

class UserHasPrescribingSuggestion extends Model
{
    protected $fillable = ['user_id', 'amount', 'annotation', 'prescribing_suggestion_id'];
    protected $table = "user_has_prescribing_suggestions";

    public function user(){
        return $this->belongsTo(FosUser::class, 'user_id');
    }

    public function prescribingSuggestion(){
        return $this->belongsTo(PrescribingSuggestion::class, 'prescribing_suggestion_id', 'id');
    }
}
