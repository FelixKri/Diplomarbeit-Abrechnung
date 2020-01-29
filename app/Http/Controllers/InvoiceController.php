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
    public function __construct()
    {
        $this->middleware('auth');
    }

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

        //Delete old ones and insert new ones
        $invoice = Invoice::where('id', request()->id)->first();

        if($invoice == null){
            return response()->json(['errors' => "Invoice not found"], 401);
        }

        $invoice->reason = request()->reason;
        $invoice->total_amount = request()->totalAmount;
        $invoice->annotation = request()->annotation;
        $invoice->updated_at = date();
        $invoice->save();

        UpdateInvPoses($invoice, request());
        
        return response()->json(['success' => 'success'], 200);
    }

    private function UpdateInvPoses($invoice, $request)
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
                    $invPoses[$j]->total_amount = $request->invoicePositions[$i]["amount"];
                    $invPoses[$j]->updated_at = date();
                    $invPoses[$j]->save();

                    UpdateUserHasInvPos($invPoses[$j], $request);

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
                $inv_pos->total_amount = $request->invoicePositions[$i]["amount"];
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
        

    }

    private function UpdateUserHasInvPos($invpos, $request)
    {
        for ($k=0; $k < sizeof(request()->invoicePositions[$i]["studentIDs"]); $k++){
            $usr_has_inv_pos = new UserHasInvoicePosition;
            $usr_has_inv_pos->invoice_position_id = $inv_pos->id;
            $usr_has_inv_pos->save();
        }
    }

    public function store(){
        
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
