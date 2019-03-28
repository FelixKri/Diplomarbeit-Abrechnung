<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fos_user;
use App\Group;

class ApiController extends Controller
{
    public function getStudentsFromClass(){
        $group = Group::where('name', request()->name)->first();

        $users = Fos_user::where('group_id', $group->id)->get();
        return $users;
    }

    public function getStudent()
    {
    	$student = Fos_user::where('id', explode(" ", request()->user)[0])->first();
    	
    	return $student;
    }
}
