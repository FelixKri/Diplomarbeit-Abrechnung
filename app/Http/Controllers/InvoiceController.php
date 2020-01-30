<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Invoice;
use App\InvoicePosition;
use App\UserHasInvoicePosition;
use App\FosUser;


class InvoiceController extends Controller
{
    /*
    
    public function __construct()
    {
        $this->middleware('auth', ['except' => [
            'show'
        ]]);
    }

    */
    public function create(){
        return view('invoice.create');
    }

    public function update(){
        $validator = Validator::make(request()->all(), [
            'date' => 'date|required',
            'author' => 'required|string',
            'reason' => 'required|string',
            'annotation' => 'string|max:255',
            'invoicePositions' => 'required|array|min:1',
            'invoicePositions.*.name' => "required|string",
            'invoicePositions.*.amount' => "required|integer",
            'invoicePositions.*.annotation' => "string",
            'invoicePositions.*.belegNr' => "required",
            'invoicePositions.*.iban' => "required_if:invoicePositions.*.paidByTeacher,true",
            'invoicePositions.*.studentIDs' => "required|array|min:1",
            'invoicePositions.*.studentIDs.*' => "integer",
            'invoicePositions.*.studentAmounts' => "required|array|min:1",
            'invoicePositions.*.studentAmounts.*' => "integer",
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        //Update invoice
        $invoice = Invoice::where('id', request()->id)->first();

        if($invoice == null){
            return response()->json(['errors' => "Invoice not found"], 401);
        }

        $invoice->date = request()->date;
        $invoice->author_id = FosUser::where('username', request()->author)->first()->id;
        $invoice->reason = request()->reason;
        $invoice->total_amount = request()->totalAmount;
        $invoice->annotation = request()->annotation;
        $invoice->save();

        //Update all invPos

        //$invPoses = InvoicePosition::where('invoice_id', $invoice->id)->get();

        for ($i=0; $i < sizeof(request()->invoicePositions); $i++) { 
            $paidByTeacher = false;
            
            if(request()->invoicePositions[$i]['paidByTeacher'] === "true"){
                $paidByTeacher = true;
            }

            //Check if this invPos already exists
            $invPos = InvoicePosition::where('invoice_id', $invoice->id)->first();

            if($invPos == null)
            {
                //Create new
                $invPos = new InvoicePosition;
            }

            $invPos->name = request()->invoicePositions[$i]['name'];
            $invPos->invoice_id = $invoice->id;
            $invPos->paid_by_teacher = $paidByTeacher;
            $invPos->iban = request()->invoicePositions[$i]['iban'];
            $invPos->document_number = request()->invoicePositions[$i]["belegNr"];
            $invPos->annotation = request()->invoicePositions[$i]["annotation"];
            $invPos->total_amount = request()->invoicePositions[$i]["amount"];
            $invPos->save();
            
            for ($j=0; $j < sizeof(request()->invoicePositions[$i]["studentIDs"]); $j++){

                $usr_has_inv_pos = UserHasInvoicePosition::where('invoice_position_id', $invPos->id)->where('user_id', request()->invoicePositions[$i]["studentIDs"][$j])->first();

                if($usr_has_inv_pos == null)
                {
                    $usr_has_inv_pos = new UserHasInvoicePosition;
                }

                $usr_has_inv_pos->user_id = request()->invoicePositions[$i]["studentIDs"][$j];
                $usr_has_inv_pos->amount = request()->invoicePositions[$i]["studentAmounts"][$j];
                $usr_has_inv_pos->invoice_position_id = $invPos->id;
                $usr_has_inv_pos->save();
            }
            
        }
        
        
        return response()->json(['success' => 'success'], 200);
    }

    public function store(){
        
        $rules = [
            'date' => 'date|required',
            'author' => 'required|string',
            'reason' => 'required|string',
            'invoicePositions' => 'required|array|min:1',
            'invoicePositions.*.name' => "required|string",
            'invoicePositions.*.amount' => "required|integer",
            'invoicePositions.*.annotation' => "",
            'invoicePositions.*.belegNr' => "required",
            'invoicePositions.*.iban' => "required_if:invoicePositions.*.paidByTeacher,true",
            'invoicePositions.*.studentIDs' => "required|array|min:1",
            'invoicePositions.*.studentIDs.*' => "integer",
            'invoicePositions.*.studentAmounts' => "required|array|min:1",
            'invoicePositions.*.studentAmounts.*' => "integer",
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

        $inv = new Invoice;
        $inv->date = request()->date;
        $inv->author_id = FosUser::where('username', request()->author)->first()->id;
        $inv->reason = request()->reason;
        $inv->total_amount = request()->totalAmount;
        $inv->annotation = request()->annotation;
        $inv->save();

        
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
            $inv_pos->save();
            
            for ($j=0; $j < sizeof(request()->invoicePositions[$i]["studentIDs"]); $j++){

                $usr_has_inv_pos = new UserHasInvoicePosition;
                $usr_has_inv_pos->user_id = request()->invoicePositions[$i]["studentIDs"][$j];
                $usr_has_inv_pos->amount = request()->invoicePositions[$i]["studentAmounts"][$j];
                $usr_has_inv_pos->invoice_position_id = $inv_pos->id;
                $usr_has_inv_pos->save();
            }
            
        }

        return response()->json(['success' => 'success'], 200);
    }

    public function show(){
        $invoices = Invoice::all();
        return view('invoice.listview', compact("invoices"));
    }

    public function getInvoices(){
        return Invoice::with('author')->get();
    }

    public function getInvoiceById($id){
        $invoice = Invoice::with('author', 'positions', 'positions.userHasInvoicePosition', 'positions.userHasInvoicePosition.user', 'positions.userHasInvoicePosition.user.group')->find($id);

        return $invoice;
    }

    public function showDetail($id){
        return view('invoice.detailView', compact('id'));
    }

    public function destroy(){
            
    }
}