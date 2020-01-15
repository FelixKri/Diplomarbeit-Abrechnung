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
    public function create(){
        return view('invoice.create');
    }

    public function store(){
        
        $validator = Validator::make(request()->all(), [
            'date' => 'date|required',
            'author' => 'required|string',
            'reason' => 'required|string',
            'annotation' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $inv = new Invoice;
        $inv->date = request()->date;
        $inv->author_id = FosUser::where('username', request()->author)->first()->id;
        $inv->reason = request()->reason;
        $inv->total_amount = 100; //TODO: Total Amount berechnen
        $inv->annotation = "test";
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
            $inv_pos->total_amount = 100; //TODO: Total Amount berechnen
            $inv_pos->save();
            
            for ($j=0; $j < sizeof(request()->invoicePositions[$i]["studentIDs"]); $j++){

                $usr_has_inv_pos = new UserHasInvoicePosition;
                $usr_has_inv_pos->user_id = request()->invoicePositions[$i]["studentIDs"][$j];
                //$usr_has_inv_pos->comment = request()->invoicePositions[$i]["studentAnnotations"][$j];
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

    public function update(){

    }

    public function destroy(){
            
    }
}
