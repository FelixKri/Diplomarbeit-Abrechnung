<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Group;
use App\Fos_user;

class PrescribingController extends Controller
{
    public function create(){
        return view('prescribing.create');
    }

    public function store(){

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
