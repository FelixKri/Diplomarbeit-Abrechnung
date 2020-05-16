<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Group;
use App\FosUser;
use App\Reason;
use Log;

class AjaxController extends Controller
{

	public function getReasons(){
		return Reason::where('active', true)->pluck("title");
	}

	public function getGroups()
	{
		return Group::All()->sortByDesc('id');
	}

	public function getAllGroups()
	{
		return Group::All()->sortByDesc('id');
	}

	public function getUserById($id){
		$u = FosUser::find($id);

		$u["amount"] = 0;
		$u["annotation"] = "";
		$u["checked"] = false;

		return response()->json($u, 200);
	}

    public function getUsers()
    {
        //Only show first 50 to not cause lag, more at once would be unwise
		$limit = 50;

    	$users = [];

        $nameFilter = request()->nameFilter;
        $classFilter = request()->classFilter;

        //Log::debug("");

        //Log::debug('NameFilter: "' . $nameFilter . '"');
        //Log::debug('ClassFilter: "' . $classFilter . '"');

        if($nameFilter == "" && $classFilter == "")
        {
        	//Do not return every existing user
            $users = [];
        }
        else if($classFilter == "")
        {
        	//Filter only after name
            $users = FosUser::where('last_name', 'LIKE', '%' . $nameFilter . '%')
                ->orWhere('first_name', 'LIKE', '%' . $nameFilter . '%')
                ->orWhere('id', 'LIKE', '%' . $nameFilter . '%')->orderBy('last_name')->take($limit)->get();
        }
        else if($nameFilter == "")
        {
	       	//filter only after class
	        //Get all possible matching classes after name
	        $classes = Group::where('name', 'LIKE', '%' . $classFilter . '%')->take($limit)->get();

	        if(count($classes) == 0)
	        	return [];

	        //Do not include searched after raw group id because it's stupid
	        $query = FosUser::where('group_id', $classes[0]['id']);

	        for($i = 1;$i < count($classes);$i++)
	        {
	        	$query->orWhere('group_id', $classes[$i]['id']);
	        }

	        $users = $query->orderBy('last_name')->take($limit)->get();
        }
        else
        {
        	//Filter both
        	//Get classes that are included
        	$classes = Group::where('name', 'LIKE', '%' . $classFilter . '%')->take($limit)->get();

	        //Do not include searched after raw group id because it's stupid
	        if(count($classes) > 0)
	        {
	        	$query = FosUser::where('group_id', $classes[0]['id']);

		        for($i = 1;$i < count($classes);$i++)
		        {
		        	$query->orWhere('group_id', $classes[$i]['id']);
		        }

		        $tempUsers = $query->orderBy('last_name')->get();
				
				$users = $tempUsers->filter(function ($item) use($nameFilter){

				   if(strpos(strtoupper($item["last_name"]), strtoupper($nameFilter)) !== false ||
				   			strpos(strtoupper($item["first_name"]), strtoupper($nameFilter)) !== false ||
				   			strpos(strtoupper (strval($item["id"])), strtoupper($nameFilter)) !== false)
				   {
				   		return $item;
				   }
				   else
				   {
				   		return;
				   }
				});
			}
            //$users = $tempUsers->where('last_name', 'LIKE', '%' . $nameFilter . '%')
              //  ->where('first_name', 'LIKE', '%' . $nameFilter . '%')
              //  ->where('id', 'LIKE', '%' . $nameFilter . '%');
        }

        foreach($users as $user)
        {
        	$user["amount"] = 0;
			$user["annotation"] = "";
			$user["checked"] = null;
        }


        return $users;
	}
}
