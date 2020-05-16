<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Prescribing;
use App\Invoice;
use App\InvoicePosition;
use App\UserHasInvoicePosition;
use App\PrescribingSuggestion;
use App\UserHasPrescribingSuggestion;
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

    public function release($id)
    {
        $in = Invoice::find($id);
        $in->saved = true;
        $in->approved = true;
        $in->save();

        $invPoses = InvoicePosition::where("invoice_id", $in->id)->get();
        $now = date("Y-m-d H:i:s");

        $userIds = [];
        $userAmounts = [];

        for ($j = 0; $j < sizeof($invPoses); $j++) {

            $uhip = UserHasInvoicePosition::Where("invoice_position_id", $invPoses[$j]->id)->get();


            //Count together
            for ($i = 0; $i < sizeof($uhip); $i++) {

                $found = false;
                for($k = 0;$k < sizeof($userIds);$k++)
                {
                    if($userIds[$k] == $uhip[$i]->user_id)
                    {
                        $found = true;
                        $userAmounts[$k] += $uhip[$i]->amount;
                        break;
                    }
                }

                if(!$found)
                {
                    array_push($userIds, $uhip[$i]->user_id);
                    array_push($userAmounts, $uhip[$i]->amount);
                }

            }

        }

        //Check if based on prescribing
        if($in->prescribing_id != null)
        {
            //If it exists, subtract studentAmounts by it
            $prescribingSug = PrescribingSuggestion::where("id", $in->prescribing_id)->first();

            $uhps = UserHasPrescribingSuggestion::where("prescribing_suggestion_id", $prescribingSug->id);

            for ($i = 0; $i < sizeof($uhps); $i++) {

                for($k = 0;$k < sizeof($userIds);$k++)
                {
                    if($userIds[$k] == $uhps[$i]->user_id)
                    {
                        $userAmounts[$k] -= $uhps[$i]->amount;
                        break;
                    }
                }

            }
        }

        //TODO
        //Set annotation depending on + or - amount
        for($k = 0;$k < sizeof($userIds);$k++)
            {
                if($userAmounts[$k] != 0)
                {
                    $p = new Prescribing();
                    //$p->title = Reason::where("id", $in->reason_id)->first()->title;
                    $p->value = $userAmounts[$k];
                    $p->user_id = $userIds[$k];
                    $p->due_until = $in->due_until;
                    $p->reason_id = $in->reason_id;
                    $p->finished = false;
                    //Nötig weil der prescribings table keine timestamps hat sondern nur den created_at
                    $p->created_at = $now;

                    if($p->value > 0)
                    {
                        $p->title = request()["annotationAdditional"];
                    }
                    else if($p->value < 0)
                    {
                        $p->title = request()["annotationCredit"];
                    }

                    $p->save();
                }
            }


        return response()->json("Success", 200);
    }

    public function update(){

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
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $inv = Invoice::find(request()->id);

        
        $inv->date = request()->date;
        $inv->due_until = request()->due_until;
        $inv->author_id = FosUser::where('username', request()->author)->first()->id;
        $inv->reason_id = Reason::where('title', request()->reason)->first()->id;
        $inv->total_amount = request()->totalAmount;
        $inv->annotation = request()->annotation;
        $inv->imported_prescribing = request()->imported_prescribing;
        $inv->save();

        //Get all old InvoicePositions and UserHasInvoicePositions and delete them
        $invPoses = InvoicePosition::where('invoice_id', request()->id)->get();
        $position_ids = $invPoses->pluck('id');

        InvoicePosition::where('invoice_id', request()->id)->delete();

        //Log::Debug($position_ids);
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
            'invoicePositions.*.studentIDs' => "array|min:1",
            'invoicePositions.*.studentIDs.*' => "integer",
            'invoicePositions.*.studentAmounts' => "required|array|min:1",
            'invoicePositions.*.studentAmounts.*.amount' => "numeric",
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

        /*
        if(request()->prescribing_id != null){
            $inv->prescribing_id = request()->prescribing_id;
        }*/

        $inv->date = request()->date;
        $inv->due_until = request()->due_until;
        $inv->author_id = FosUser::where('username', request()->author)->first()->id;
        $inv->reason_id = Reason::where('title', request()->reason)->first()->id;
        $inv->total_amount = request()->totalAmount;
        $inv->annotation = request()->annotation;
        $inv->imported_prescribing = request()->imported_prescribing;
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
            
            for ($j=0; $j < sizeof(request()->invoicePositions[$i]["studentAmounts"]); $j++){

                $usr_has_inv_pos = new UserHasInvoicePosition;
                $usr_has_inv_pos->user_id = request()->invoicePositions[$i]["studentAmounts"][$j]["student"]["id"];
                $usr_has_inv_pos->amount = request()->invoicePositions[$i]["studentAmounts"][$j]["amount"];
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