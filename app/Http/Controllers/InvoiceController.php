<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Prescribing;
use App\Invoice;
use App\InvoicePosition;
use App\UserHasInvoicePosition;
use App\FosUser;
use App\Reason;
use Log;


class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(){

        $reasons = Reason::all();

        return view('invoice.create', compact("reasons"));
    }

    public function updateOld(){
        //Log::debug("Request:");
        //Log::debug(request());

        $rules = [
            'date' => 'date|required',
            'due_until' => 'date|after:today|required|date_format:Y-m-d',
            'author' => 'required|string',
            'reason' => 'required',
            'invoicePositions' => 'required|array|min:1',
            'invoicePositions.*.name' => "required|string",
            'invoicePositions.*.annotation' => "",
            'invoicePositions.*.belegNr' => "required",
            'invoicePositions.*.iban' => "required_if:invoicePositions.*.paidByTeacher,true",
            'invoicePositions.*.studentIDs' => "required|array|min:1",
            'invoicePositions.*.studentIDs.*' => "integer",
            'invoicePositions.*.studentAmounts' => "required|array|min:1",
            'invoicePositions.*.studentAmounts.*' => "numeric",
        ];

        $messages = [
            'required'    => 'Das Feld muss ausgefüllt werden.',
            'after' => 'Das Feld muss nach dem heutigen Tag liegen',
            'date' => 'Das Feld muss ein gültiges Datum enthalten',
            'required_without' => "Bitte entweder einen Grund oder Grundvorschlag auswählen",
            'min' => 'Bitte mindestens einen Schüler zur Vorschreibung hinufügen',
            'required_if' => 'Bitte einen IBan für die Überweisung angeben',
        ];
        

        $validator = Validator::make(request()->all(), $rules, $messages);

        
        if ($validator->fails()) {

            $newErrors = [];
            $errors = $validator->errors();

            $keys = $errors->keys();

            for($i = 0;$i < sizeof($keys);$i++)
            {
                $key = $keys[$i];

                $strpos = strpos($key, "invoicePositions.");

                if($strpos === false)
                {
                    //Not found
                }
                else
                {
                    //Cut away invoicePositions.
                    $keyIndex = str_replace("invoicePositions.", "", $key);

                    //Get the word after the number to know what it was(iban, name ...)
                    $type = substr($keyIndex, strpos($keyIndex, "."), strlen($keyIndex) - strpos($keyIndex, "."));
                    //Cut it until next . to get number
                    $keyIndex = substr($keyIndex, 0, strpos($keyIndex, "."));

                    //Convert to number
                    $keyIndex = intval($keyIndex);

                    //Get the actual id of the invoicePosition
                    $newKeyId = request()->invoicePositions[$keyIndex]["id"];

                    $newKey = "invoicePositions." . $newKeyId . $type;

                    $newErrors[$newKey] = $errors->first($key);
                }
            }

            return response()->json(['errors' => $newErrors], 401);
        } 

        $invoice = Invoice::where('id', request()->id)->first();

        if($invoice == null){
            return response()->json(['errors' => "Invoice not found"], 401);
        }

        $invoice->reason_id = Reason::where('title', request()->reason)->first()->id;
        $invoice->total_amount = request()->totalAmount;
        $invoice->annotation = request()->annotation;
        $invoice->due_until = request()->due_until;

        $invoice->save();

        $this->UpdateInvPoses($invoice, request());
        
        Log::debug("Successfully saved update");
        return response()->json(['success' => 'success'], 200);
    }

    public function release($id)
    {
        $in = Invoice::find($id);
        $in->saved = true;
        $in->approved = true;
        $in->save();

        $invPoses = InvoicePosition::where("invoice_id", $in->id)->get();
        $now = date("Y-m-d H:i:s");

        for ($j = 0; $j < sizeof($invPoses); $j++) {

            $uhip = UserHasInvoicePosition::Where("invoice_position_id", $invPoses[$j]->id)->get();

            //Make real prescribings
            for ($i = 0; $i < sizeof($uhip); $i++) {

                $p = new Prescribing();
                $p->title = $invPoses[$j]->name;
                $p->value = $uhip[$i]->amount;
                $p->user_id = $uhip[$i]->user_id;
                $p->due_until = $in->due_until;
                $p->reason_id = $in->reason_id;
                $p->finished = false;
                //Nötig weil der prescribings table keine timestamps hat sondern nur den created_at
                $p->created_at = $now;
                $p->save();
            }
        }

        return response()->json("Success", 200);
    }

    public function update(){
        $rules = [
            'date' => 'date|required',
            'due_until' => 'date|after:today|required|date_format:Y-m-d',
            'author' => 'required|string',
            'reason' => 'required',
            'invoicePositions' => 'required|array|min:1',
            'invoicePositions.*.name' => "required|string",
            'invoicePositions.*.annotation' => "",
            'invoicePositions.*.belegNr' => "required",
            'invoicePositions.*.iban' => "required_if:invoicePositions.*.paidByTeacher,true",
            'invoicePositions.*.studentIDs' => "required|array|min:1",
            'invoicePositions.*.studentIDs.*' => "integer",
            'invoicePositions.*.studentAmounts' => "required|array|min:1",
            'invoicePositions.*.studentAmounts.*' => "numeric",
        ];

        $messages = [
            'required'    => 'Das Feld muss ausgefüllt werden.',
            'after' => 'Das Feld muss nach dem heutigen Tag liegen',
            'date' => 'Das Feld muss ein gültiges Datum enthalten',
            'required_without' => "Bitte entweder einen Grund oder Grundvorschlag auswählen",
            'min' => 'Bitte mindestens einen Schüler zur Vorschreibung hinufügen',
            'required_if' => 'Bitte einen IBan für die Überweisung angeben',
        ];

        $validator = Validator::make(request()->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $inv = Invoice::find(request()->id);
        $inv->date = request()->date;
        $inv->due_until = request()->due_until;
        $inv->author_id = FosUser::where('username', request()->author)->first()->id;
        $inv->reason_id = Reason::where('title', request()->reason)->first()->id;
        $inv->total_amount = request()->totalAmount;
        $inv->annotation = request()->annotation;
        $inv->save();

        //Get all old InvoicePositions and UserHasInvoicePositions and delete them
        $position_ids = InvoicePosition::where('invoice_id', request()->id)->pluck('id');
        InvoicePosition::where('invoice_id', request()->id)->delete();
        UserHasInvoicePosition::where('invoice_position_id', $position_ids)->delete();

        //Create new Invoice Positions and UserHasInvoicePositions
        foreach(request()->invoicePositions as $invoicePosition){
            $paidByTeacher = false;
            
            if($invoicePosition['paidByTeacher'] === "true"){
                $paidByTeacher = true;
            }

            $inv_pos = new InvoicePosition;
            $inv_pos->name = $invoicePosition['name'];
            $inv_pos->invoice_id = $inv->id;
            $inv_pos->paid_by_teacher = $paidByTeacher;
            $inv_pos->iban = $invoicePosition['iban'];
            $inv_pos->document_number = $invoicePosition["belegNr"];
            $inv_pos->annotation = $invoicePosition["annotation"];
            $inv_pos->total_amount = array_sum($invoicePosition["studentAmounts"]);
            $inv_pos->position_id = $invoicePosition["position_id"];
            $inv_pos->save();

            for ($j=0; $j < sizeof($invoicePosition["studentIDs"]); $j++){
                $usr_has_inv_pos = new UserHasInvoicePosition;
                $usr_has_inv_pos->user_id = $invoicePosition["studentIDs"][$j];
                $usr_has_inv_pos->amount = $invoicePosition["studentAmounts"][$j];
                $usr_has_inv_pos->invoice_position_id = $inv_pos->id;
                $usr_has_inv_pos->save();
            }
        }

        return response()->json(['success' => 'success'], 200);

    }

    public function UpdateInvPoses($invoice, $request)
    {
        //Get all old InvPoses
        $invPoses = InvoicePosition::where('invoice_id', $invoice->id)->get();

        //See which ones are to update
        for ($i=0; $i < sizeof($request->invoicePositions); $i++) { 
            //See if this invoicePosition was updated, not created
            $updated = false;

            for ($j=0; $j < sizeof($invPoses); $j++) {

                if($invPoses[$j]["id"] == $request->invoicePositions[$i]["id"])
                {
                    $updated = true;
                    $paidByTeacher = false;
                    if($request->invoicePositions[$i]['paidByTeacher'] === "true"){
                        $paidByTeacher = true;
                    }
                    //Update this one
                    $invPoses[$j]->name = $request->invoicePositions[$i]['name'];
                    $invPoses[$j]->paid_by_teacher = $paidByTeacher;
                    $invPoses[$j]->iban = $request->invoicePositions[$i]['iban'];
                    $invPoses[$j]->document_number = $request->invoicePositions[$i]["belegNr"];
                    $invPoses[$j]->annotation = $request->invoicePositions[$i]["annotation"];
                    //$invPoses[$j]->total_amount = $request->invoicePositions[$i]["amount"];

                    $invPoses[$j]->total_amount = array_sum($request->invoicePositions[$i]["studentAmounts"]);

                    $invPoses[$j]->save();

                    $this->UpdateUserHasInvPos($invPoses[$j], $request, $i);

                    break;
                }
            }

            if(!$updated)
            {
                //Was created, so create a new one
                $paidByTeacher = false;
            
                if($request->invoicePositions[$i]['paidByTeacher'] === "true"){
                    $paidByTeacher = true;
                }

                $inv_pos = new InvoicePosition;
                $inv_pos->name = $request->invoicePositions[$i]['name'];
                $inv_pos->invoice_id = $invoice->id;
                $inv_pos->paid_by_teacher = $paidByTeacher;
                $inv_pos->iban = $request->invoicePositions[$i]['iban'];
                $inv_pos->document_number = $request->invoicePositions[$i]["belegNr"];
                $inv_pos->annotation = $request->invoicePositions[$i]["annotation"];
                $inv_pos->total_amount = array_sum($request->invoicePositions[$i]["studentAmounts"]);
                $inv_pos->position_id = $request->invoicePositions[$i]["position_id"];
                $inv_pos->save();

                //Create user has invposes
                for ($j=0; $j < sizeof($request->invoicePositions[$i]["studentIDs"]); $j++){
                    $usr_has_inv_pos = new UserHasInvoicePosition;
                    $usr_has_inv_pos->user_id = $request->invoicePositions[$i]["studentIDs"][$j];
                    $usr_has_inv_pos->amount = $request->invoicePositions[$i]["studentAmounts"][$j];
                    $usr_has_inv_pos->invoice_position_id = $inv_pos->id;
                    $usr_has_inv_pos->save();
                }
            }

        }

        //Check for deleted invoicePositions and delete them
        //They are in database but not in request
        $invPoses = InvoicePosition::where('invoice_id', $invoice->id)->get();

        for($i = 0;$i < sizeof($invPoses);$i++)
        {
            $exists = false;
            //Look if they are in request
            for($j=0;$j < sizeof($request->invoicePositions);$j++)
            {
                if($request->invoicePositions[$j]["id"] == $invPoses[$i]["id"])
                {
                    $exists = true;
                    break;
                }
            }

            if(!$exists)
            {
                //Delete them AND the corresponding userHasInvPoses
                $userHasInvPoses = UserHasInvoicePosition::where('invoice_position_id', $invPoses[$i]["id"])->get();

                for($j=0;$j < sizeof($userHasInvPoses);$j++)
                {
                    $userHasInvPoses[$j]->delete();
                }

                $invPoses[$i]->delete();
            }
        }
    }

    private function UpdateUserHasInvPos($invpos, $request, $i)
    {
        //Log::debug("Updating UserHasInvPoses");

        $userHasInvPoses = UserHasInvoicePosition::where('invoice_position_id', $invpos["id"])->get();

        for ($k=0; $k < sizeof($request->invoicePositions[$i]["studentIDs"]); $k++){
            
            //Search if it already exists and needs to be updated
            $found = false;
            for($l=0;$l < sizeof($userHasInvPoses);$l++)
            {
                
                if($userHasInvPoses[$l]["user_id"] == $request->invoicePositions[$i]["studentIDs"][$k])
                {
                    //Same user_id, update it
                    $userHasInvPoses[$l]->amount = $request->invoicePositions[$i]["studentAmounts"][$k];
                    $userHasInvPoses[$l]->save();

                    $found = true;
                    break;
                }
            }

            if(!$found)
            {
                //Create new one
                $usr_has_inv_pos = new UserHasInvoicePosition;
                //Log::debug("Creating new UserHasInvPos, user_id: ");
                $usr_has_inv_pos->user_id = $request->invoicePositions[$i]["studentIDs"][$k];
                $usr_has_inv_pos->amount = $request->invoicePositions[$i]["studentAmounts"][$k];
                $usr_has_inv_pos->invoice_position_id = $invpos->id;
                $usr_has_inv_pos->save();
            }
        }

        //Check if users got deleted aka they have a userHasInvPos in database but not in request
        $userHasInvPoses = UserHasInvoicePosition::where('invoice_position_id', $invpos["id"])->get();

        for($j=0;$j < sizeof($userHasInvPoses);$j++)
        {
            //Check if their student id exists in request
            $exists = false;

            for($k=0;$k < sizeof($request->invoicePositions[$i]["studentIDs"]);$k++)
            {
                if($request->invoicePositions[$i]["studentIDs"][$k] == $userHasInvPoses[$j]["user_id"])
                {
                    $exists = true;
                    break;
                }
            }

            if(!$exists)
            {
                //Does not exist, delete it
                $userHasInvPoses[$j]->delete();
            }
        }

    }

    public function store(){
        
        $rules = [
            'date' => 'date|required',
            'due_until' => 'date|after:today|required|date_format:Y-m-d',
            'author' => 'required|string',
            'reason' => 'required',
            'invoicePositions' => 'required|array|min:1',
            'invoicePositions.*.name' => "required|string",
            'invoicePositions.*.amount' => "required|numeric",
            'invoicePositions.*.annotation' => "",
            'invoicePositions.*.belegNr' => "required",
            'invoicePositions.*.iban' => "required_if:invoicePositions.*.paidByTeacher,true",
            'invoicePositions.*.studentIDs' => "required|array|min:1",
            'invoicePositions.*.studentIDs.*' => "integer",
            'invoicePositions.*.studentAmounts' => "required|array|min:1",
            'invoicePositions.*.studentAmounts.*' => "numeric",
        ];

        $messages = [
            'required'    => 'Das Feld muss ausgefüllt werden.',
            'after' => 'Das Feld muss nach dem heutigen Tag liegen',
            'date' => 'Das Feld muss ein gültiges Datum enthalten',
            'required_without' => "Bitte entweder einen Grund oder Grundvorschlag auswählen",
            'min' => 'Bitte mindestens einen Schüler zur Vorschreibung hinufügen',
            'required_if' => 'Bitte einen IBan für die Überweisung angeben',
        ];
        

        $validator = Validator::make(request()->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        if(request()->id == null){
            $inv = new Invoice;
        }else{
            $inv = Invoice::find(request()->id);
        }

        $inv->date = request()->date;
        $inv->due_until = request()->due_until;
        $inv->author_id = FosUser::where('username', request()->author)->first()->id;
        $inv->reason_id = Reason::where('title', request()->reason)->first()->id;
        $inv->total_amount = request()->totalAmount;
        $inv->annotation = request()->annotation;
        $inv->save();

        if(request()->id != null){
            InvoicePosition::where('invoice_id', request()->id)->delete();
        }

        for ($i=0; $i < sizeof(request()->invoicePositions); $i++) { 
            $paidByTeacher = false;
            
            if(request()->invoicePositions[$i]['paidByTeacher'] === "true"){
                $paidByTeacher = true;
            }

            $inv_pos = new InvoicePosition;
            $inv_pos->name = request()->invoicePositions[$i]['name'];
            $inv_pos->invoice_id = $inv->id;
            $inv_pos->paid_by_teacher = $paidByTeacher;
            $inv_pos->iban = request()->invoicePositions[$i]['iban'];
            $inv_pos->document_number = request()->invoicePositions[$i]["belegNr"];
            $inv_pos->annotation = request()->invoicePositions[$i]["annotation"];
            $inv_pos->total_amount = request()->invoicePositions[$i]["amount"];
            $inv_pos->position_id = request()->invoicePositions[$i]["id"];
            $inv_pos->save();
            
            for ($j=0; $j < sizeof(request()->invoicePositions[$i]["studentIDs"]); $j++){

                $usr_has_inv_pos = new UserHasInvoicePosition;
                $usr_has_inv_pos->user_id = request()->invoicePositions[$i]["studentIDs"][$j];
                $usr_has_inv_pos->amount = request()->invoicePositions[$i]["studentAmounts"][$j];
                $usr_has_inv_pos->invoice_position_id = $inv_pos->id;
                $usr_has_inv_pos->save();
            }
            
        }

        return response()->json($inv->id, 200);
    }

    public function show(){
        $invoices = Invoice::all();
        return view('invoice.listview', compact("invoices"));
    }

    public function getInvoices(){
        return Invoice::with('author')->get();
    }

    public function getInvoiceById($id){
        $invoice = Invoice::with('author', 'positions', 'positions.userHasInvoicePosition', 'positions.userHasInvoicePosition.user', 'positions.userHasInvoicePosition.user.group', 'reason')->find($id);        

        return $invoice;
    }

    public function showDetail($id){

        $reasons = Reason::all();
        
        return view('invoice.detailView', compact('id', 'reasons'));
    }

    public function destroy(){
            
    }

    public function setFinished($id)
    {
        $i = Invoice::find($id);
        $i->saved = true;
        $i->approved = false;
        $i->save();

        return response()->json("success", 200);
    }

    public function reject($id)
    {
        $i = Invoice::find($id);
        $i->saved = false;
        $i->approved = false;
        $i->save();

        return response()->json("Successfully rejected", 200);

    }
}