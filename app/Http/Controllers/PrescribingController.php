<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use App\Group;
use App\FosUser;
use App\PrescribingSuggestion;
use App\Prescribing;
use App\UserHasPrescribingSuggestion;
use App\Reason;
use Log;

use Illuminate\Support\Facades\Validator;

class PrescribingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        /*$this->middleware('auth', ['except' => [
            'store',
            'update'
        ]]);*/
    }
    
    public function create(){

        $reasons = Reason::all();
        return view('prescribing.create', compact("reasons"));
    }

    public function store()
    {

        Log::debug("Saving prescribing. Request:");
        Log::debug(request());

        $rules = array(
            'date' => 'date|required',
            'due_until' => 'date|after:today|required|date_format:Y-m-d',
            'reason' => 'required_without:reason_suggestion',
            'reason_suggestion' => 'required_without:reason',
            'title' => 'required',
            'description' => 'required',
            'author' => 'required|string',
            'students' => 'required|array|min:1',
            'students.*' => 'required|integer|distinct|min:1',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|min:1',
            'annotation' => 'required|array',
            'annotation.*' => 'string|nullable'
        );

        $messages = [
            'required'    => 'Das Feld muss ausgefüllt werden.',
            'after' => 'Das Feld muss nach dem heutigen Tag liegen',
            'date' => 'Das Feld muss ein gültiges Datum enthalten',
            'required_without' => "Bitte entweder einen Grund oder Grundvorschlag auswählen",
            'min' => 'Bitte mindestens einen Schüler zur Vorschreibung hinufügen'
        ];
        

        $validator = Validator::make(request()->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }


        if(request()->id == null){
            $presc = new PrescribingSuggestion();
        }else{
            $presc = PrescribingSuggestion::find(request()->id);
        }

        $presc->date = request()->date;
        $presc->total_amount = request()->totalAmount;
        $presc->due_until = request()->due_until;
        if (request()->reason) {
            $presc->reason_id = Reason::where('title', request()->reason)->first()->id;
        }
        $presc->reason_suggestion = request()->reason_suggestion;
        $presc->title = request()->title;
        $presc->description = request()->description;
        $presc->author_id = FosUser::where('username', request()->author)->first()->id;
        $presc->save();

        if(request()->id != null){
            UserHasPrescribingSuggestion::where('prescribing_suggestion_id', request()->id)->delete();
        }

        for ($i = 0; $i < sizeof(request()->students); $i++) {
            
            $user_has_presc = new UserHasPrescribingSuggestion;
            $user_has_presc->user_id = request()->students[$i];

            Log::debug("Saving prescribing for a stundent with amount:");
            Log::debug(request()->amount[$i]);

            $user_has_presc->amount = (float) request()->amount[$i];
            $user_has_presc->annotation = request()->annotation[$i];
            $user_has_presc->prescribing_suggestion_id = $presc->id;
            $user_has_presc->save();
        }

        return response()->json($presc->id, 200);
    }

    public function update()
    {
        $rules = array(
            'date' => 'date|required',
            'due_until' => 'date|after:today|required|date_format:Y-m-d',
            'reason' => 'required_without:reason_suggestion',
            'reason_suggestion' => 'required_without:reason',
            'title' => 'required',
            'description' => 'required',
            'author' => 'required|string',
            'students' => 'required|array|min:1',
            'students.*' => 'required|integer|distinct|min:1',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|min:1',
            'annotation' => 'required|array',
            'annotation.*' => 'string|nullable'
        );

        $messages = [
            'required'    => 'Das Feld muss ausgefüllt werden.',
            'after' => 'Das Feld muss nach dem heutigen Tag liegen',
            'date' => 'Das Feld muss ein gültiges Datum enthalten',
            'required_without' => "Bitte entweder einen Grund oder Grundvorschlag auswählen",
            'min' => 'Bitte mindestens einen Schüler zur Vorschreibung hinufügen'
        ];
        

        $validator = Validator::make(request()->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $presc = PrescribingSuggestion::find(request()->id);
        $presc->date = request()->date;
        $presc->due_until = request()->due_until;
        if(request()->reason){
            $presc->reason_id = Reason::where('title', request()->reason)->first()->id;
            $presc->reason_suggestion = null;
        }else{
            $presc->reason_suggestion = request()->reason_suggestion;
        }
        $presc->title = request()->title;
        $presc->description = request()->description;
        $presc->author_id = FosUser::where('username', request()->author)->first()->id;
        $presc->total_amount = request()->totalAmount;
        $presc->save();

        $user_has_presc = UserHasPrescribingSuggestion::where("prescribing_suggestion_id", request()->id)->delete();

        for ($i = 0; $i < sizeof(request()->students); $i++) {
                $user_has_presc = new UserHasPrescribingSuggestion();
                $user_has_presc->user_id = request()->students[$i];
                $user_has_presc->amount = (float) request()->amount[$i];
                $user_has_presc->annotation = request()->annotation[$i];
                $user_has_presc->prescribing_suggestion_id = $presc->id;
                $user_has_presc->save();
        }

        return response()->json($presc->id, 200);
    }

    public function release($id)
    {
        $ps = PrescribingSuggestion::find($id);
        $ps->approved = true;
        $ps->save();

        $now = date("Y-m-d H:i:s");

        $uhps = $ps->positions;

        //Make real prescribings
        for ($i = 0; $i < sizeof($uhps); $i++) {

            $p = new Prescribing();
            $p->title = $ps->title;
            $p->value = $uhps[$i]->amount;
            $p->user_id = $uhps[$i]->user_id;
            $p->due_until = $ps->due_until;
            $p->reason_id = $ps->reason_id;
            $p->finished = false;
            //Nötig weil der prescribings table keine timestamps hat sondern nur den created_at
            $p->created_at = $now;
            $p->save();
        }

        return response()->json("success", 200);
    }

    public function setFinished($id)
    {
        $p = PrescribingSuggestion::find($id);
        $p->finished = true;
        $p->approved = false;
        $p->save();

        return response()->json("success", 200);
    }

    public function reject($id)
    {
        $p = PrescribingSuggestion::find($id);
        $p->finished = false;
        $p->approved = false;
        $p->save();

        return response()->json("Successfully rejected", 200);

    }

    public function destroy($id)
    {
    }

    public function show()
    {
        $presc = PrescribingSuggestion::all();
        return view('prescribing.listview', compact("presc"));
    }

    public function showDetail($id)
    {
        return view('prescribing.showDetail', compact('id'));
    }

    public function getPrescribingById($id)
    {
        $prescribing = PrescribingSuggestion::with('author', 'positions', 'reason', 'positions.user', 'positions.user.group')->find($id);
        return $prescribing;
    }

    public function getPrescribings()
    {
        return PrescribingSuggestion::with('positions.user')->get();
    }

    public function getByReasonId($id){
        $prescribings = Reason::find($id)->prescribings()->with('author', 'positions', 'reason', 'positions.user', 'positions.user.group')->get();

        return response()->json($prescribings, 200);
    }
}
