<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Invoice;
use App\Invoice_Position;
use App\User_has_Invoice_Position;


class InvoiceController extends Controller
{
    public function create(){
        return view('invoice.create');
    }

    public function store(){
        dd(request()->all());
    }
}
