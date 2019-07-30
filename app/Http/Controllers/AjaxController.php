<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Group;
use App\Fos_user;
use Log;

class AjaxController extends Controller
{

	public function getAllGroups()
	{
		return Group::All()->sortByDesc('id');
	}

    public function getUsers()
    {
        //Only show first 50 to not cause lag, more at once would be unwise
		$limit = 50;

    	$users = [];

        $nameFilter = request()->nameFilter;
        $classFilter = request()->classFilter;

        Log::debug('NameFilter: "' . $nameFilter . '"');
        Log::debug('ClassFilter: "' . $classFilter . '"');

        Log::debug(request());

        if($nameFilter == "" && $classFilter == "")
        {
        	//Do not return every existing user
            $users = [];
        }
        else if($classFilter == "")
        {
        	//Filter only after name
            $users = Fos_user::where('last_name', 'LIKE', '%' . $nameFilter . '%')
                ->orWhere('first_name', 'LIKE', '%' . $nameFilter . '%')
                ->orWhere('id', 'LIKE', '%' . $nameFilter . '%')->take($limit)->get();
        }
        else if($nameFilter == "")
        {
       	//filter only after class
        //Get all possible matching classes after name
        $classes = Group::where('name', 'LIKE', '%' . $classFilter . '%')->take($limit)->get();

        if(count($classes) == 0)
        	return [];

        //Do not include searched after raw group id because it's stupid
        $query = Fos_user::where('group_id', $classes[0]['id']);

        for($i = 1;$i < count($classes);$i++)
        {
        	$query->orWhere('group_id', $classes[$i]['id']);
        }

        $users = $query->take($limit)->get();
        }
        else
        {
            //Filter both later
            $users = [];
        }

        return $users;
    }
}
