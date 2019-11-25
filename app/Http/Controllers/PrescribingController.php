<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use App\Group;
use App\Fos_user;
use App\Prescribing_Suggestion;
use App\User_has_Prescribing_Suggestion;
use App\Reason;

use Illuminate\Support\Facades\Validator;

class PrescribingController extends Controller
{
    public function create(){
        $reasons = Reason::all();
        return view('prescribing.create', compact("reasons"));
    }

    public function store(){

        return request()->all();

        $validator = Validator::make(request()->all(), [
            'date' => 'date|required',
            'due_until' => 'date|after:today|required|date_format:Y-m-d',
            'reason' => 'required_without:reason_suggestion',
            'reason_suggestion' => 'required_without:reason',
            'title' => 'string|required',
            'description' => 'string|required',
            'author' => 'required|string',
            'students' => 'required|array|min:1',
            'students.*' => 'required|integer|distinct|min:1',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|integer|min:1',
            'annotation' => 'required|array',
            'annotation.*' => 'string|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }
        

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
        return response()->json(['success' => 'success'], 200);
    }

    public function update(){

    }

    public function destroy(){

    }
}
