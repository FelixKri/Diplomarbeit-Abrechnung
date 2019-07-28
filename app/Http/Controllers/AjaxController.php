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

    public function getUsers()
    {
        $nameFilter = request()->nameFilter;
        $classFilter = request()->classFilter;

        Log::debug('NameFilter: "' . $nameFilter . '"');
        Log::debug('ClassFilter: "' . $classFilter . '"');

        Log::debug(request());

        if($nameFilter == "" && $classFilter == "")
        {
            return [];
        }
        else if($classFilter == "")
        {
            return Fos_user::where('last_name', 'LIKE', '%' . $nameFilter . '%')
                ->orWhere('first_name', 'LIKE', '%' . $nameFilter . '%')
                ->orWhere('id', 'LIKE', '%' . $nameFilter . '%')->get();
        }
        else if($nameFilter == "")
        {
            $group = Group::where('name', 'LIKE', $classFilter)->first();

            if($group != null)
            {
            return Fos_user::where('group_id', 'LIKE', '%' . $classFilter . '%')
                ->orWhere('group_id', 'LIKE', '%' . 
                    $group->id
                     . '%')->get();
            }
            else
            {
                return [];
            }
        }
        else
        {
            //Filter both later
            return [];
        }
    }
}
