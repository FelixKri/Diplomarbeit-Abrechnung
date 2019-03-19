<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Group;
use App\Fos_user;
use App\Prescribing_Suggestion;
use App\User_has_Prescribing_Suggestion;
use App\Reason;

class PrescribingController extends Controller
{
    public function create(){
        $reasons = Reason::all();
        return view('prescribing.create', compact("reasons"));
    }

    public function store(){
        $presc = New Prescribing_Suggestion();
        $presc->due_until = request()->due_until;
        $presc->reason_id = Reason::where('title', request()->reason)->first()->id;
        $presc->reason_suggestion = request()->reason_suggestion;
        $presc->title = request()->title;
        $presc->description = request()->description;
        $presc->author_id = Fos_user::where('username', request()->author)->first()->id;
        $presc->save();

        for ($i=0; $i < sizeof(request()->students); $i++) { 
            $user_has_presc = new User_has_Prescribing_Suggestion;
            $user_has_presc->user_id = request()->students[$i];
            $user_has_presc->amount = (float)request()->amount[$i];
            $user_has_presc->annotation = request()->annotation[$i];
            $user_has_presc->prescribing_suggestion_id = $presc->id;
            $user_has_presc->save();
        }

        dd($presc);
    }

    public function update(){

    }

    public function destroy(){

    }

    public function autocompleteClass(){
        $term = request()->term;
        $group = Group::where('name', 'LIKE', '%' . $term . '%')->get();
        if (count($group) == 0) {
            $searchResult = ["Keine Treffer"];
            return $searchResult;
        } else {
            foreach ($group as $key => $value) {
                $searchResult[] =  $value->name;
            }
        }
        return $searchResult;
    }

    public function autocompleteUser(){
        $term = request()->term;
        $user = Fos_user::where('last_name', 'LIKE', '%' . $term . '%')
            ->orWhere('first_name', 'LIKE', '%' . $term . '%')
            ->orWhere('id', 'LIKE', '%' . $term . '%')->get();
        if (count($user) == 0) {
            $searchResult = ["Keine Treffer"];
            return $searchResult;
        } else {
            foreach ($user as $key => $value) {
                $searchResult[] = $value->id . " | " . $value->last_name . " " . $value->first_name . " | " . $value->group->name;
            }
        }
        return $searchResult;
    }
}
