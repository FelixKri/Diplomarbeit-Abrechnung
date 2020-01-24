<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use App\Group;
use App\FosUser;
use App\PrescribingSuggestion;
use App\UserHasPrescribingSuggestion;
use App\Reason;

use Illuminate\Support\Facades\Validator;

class PrescribingController extends Controller
{
    public function create(){
        $reasons = Reason::all();
        return view('prescribing.create', compact("reasons"));
    }

    public function store(){

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
            'amount.*' => 'required|min:1',
            'annotation' => 'required|array',
            'annotation.*' => 'string|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }
        

        $presc = New PrescribingSuggestion();
        $presc->date = request()->date;
        $presc->total_amount = request()->totalAmount;
        $presc->due_until = request()->due_until;
        if(request()->reason){
            $presc->reason_id = Reason::where('title', request()->reason)->first()->id;
        }
        $presc->reason_suggestion = request()->reason_suggestion;
        $presc->title = request()->title;
        $presc->description = request()->description;
        $presc->author_id = FosUser::where('username', request()->author)->first()->id;
        $presc->save();

        for ($i=0; $i < sizeof(request()->students); $i++) { 
            $user_has_presc = new UserHasPrescribingSuggestion;
            $user_has_presc->user_id = request()->students[$i];
            $user_has_presc->amount = (float)request()->amount[$i];
            $user_has_presc->annotation = request()->annotation[$i];
            $user_has_presc->prescribing_suggestion_id = $presc->id;
            $user_has_presc->save();
        }

        return response()->json($presc->id, 200);
    }

    public function update(){
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
            'amount.*' => 'required|min:1',
            'annotation' => 'required|array',
            'annotation.*' => 'string|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $presc = PrescribingSuggestion::find(request()->id);
        $presc->date = request()->date;
        $presc->due_until = request()->due_until;
        $presc->reason_id = Reason::where('title', request()->reason)->first()->id;
        $presc->reason_suggestion = request()->reason_suggestion;
        $presc->title = request()->title;
        $presc->description = request()->description;
        $presc->author_id = FosUser::where('username', request()->author)->first()->id;
        $presc->save();

        for ($i=0; $i < sizeof(request()->students); $i++) { 
            $user_has_presc = UserHasPrescribingSuggestion::find(request()->positionIds[$i]);
            $user_has_presc->user_id = request()->students[$i];
            $user_has_presc->amount = (float)request()->amount[$i];
            $user_has_presc->annotation = request()->annotation[$i];
            $user_has_presc->prescribing_suggestion_id = $presc->id;
            $user_has_presc->save();
        }

        return response()->json($presc->id, 200);
    }

    public function setApproved($id){
        $p = PrescribingSuggestion::find($id);
        $p->finished = false;
        $p->approved = true;
        $p->save();

        return response()->json("success", 200);
    }

    public function setFinished($id){
        $p = PrescribingSuggestion::find($id);
        $p->finished = true;
        $p->approved = false;
        $p->save();

        return response()->json("success", 200);
    }

    public function reject($id){

    }

    public function release($id){

    }

    public function destroy($id){
        
    }

    public function show(){
        $presc = PrescribingSuggestion::all();
        return view('prescribing.listview', compact("presc"));
    }

    public function showDetail($id){
        return view('prescribing.showDetail', compact('id'));
    }

    public function getPrescribingById($id){
        $prescribing = PrescribingSuggestion::with('author', 'positions', 'reason', 'positions.user', 'positions.user.group')->find($id);
        return $prescribing;
    }

    public function getPrescribings(){
        return PrescribingSuggestion::with('positions.user')->get();
    }
}
