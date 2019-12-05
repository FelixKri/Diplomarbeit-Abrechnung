<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Invoice;
use App\InvoicePosition;
use App\User_hasInvoice_Position;
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
            'iban' => 'iban',
            'reason' => 'required|string',
            'annotation' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $inv = new Invoice;
        $inv->date = request()->date;
        $inv->author_id = FosUser::where('username', request()->author)->first()->id;
        $inv->iban = request()->iban;
        $inv->reason_id = 1; //TODO: Reason Selection Text
        $inv->total_amount = 100; //TODO: Total Amount berechnen
        $inv->annotation = "test";
        $inv->save();

        
        for ($i=0; $i < sizeof(request()->invoicePositions); $i++) { 
            $inv_pos = new InvoicePosition;
            $inv_pos->designation = request()->invoicePositions[$i]['name'];
            $inv_pos->invoice_id = $inv->id;
            $inv_pos->paid_by_teacher = true;
            $inv_pos->document_number = request()->invoicePositions[$i]["belegNr"];
            $inv_pos->total_amount = 100; //TODO: Total Amount berechnen
            $inv_pos->save();
            
            for ($j=0; $j < sizeof(request()->invoicePositions[$i]["studentIDs"]); $j++){
                $usr_has_inv_pos = new UserHasInvoicePosition;
                $usr_has_inv_pos->user_id = request()->invoicePositions[$i]["studentIDs"][$j];
                $usr_has_inv_pos->comment = request()->invoicePositions[$i]["studentAnnotations"][$j];
                $usr_has_inv_pos->amount = request()->invoicePositions[$i]["studentAmounts"][$j];
                $usr_has_inv_pos->invoice_position_id = $inv_pos->id;
                $usr_has_inv_pos->save();
            }
            
        }

        return response()->json(['success' => 'success'], 200);
    }
}
