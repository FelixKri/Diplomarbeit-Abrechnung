<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FosUser;
use App\Group;

class ApiController extends Controller
{
    public function getStudentsFromClass(){
        $group = Group::where('name', request()->name)->first();

        $users = FosUser::where('group_id', $group->id)->get();
        return $users;
    }

    public function getStudent()
    {
        //Maybe make safer / not only with autocomplete used
    	$student = FosUser::where('id', explode(" ", request()->user)[0])->first();
    	
    	return $student;
    }

    
}
