@extends('layout.master')

@section('content')
    <div class="container">
        <h1>Neue Abrechnung erstellen</h1>
        <form action="/invoice/new" method="post">
            <div class="form-group">
                <label for="reason">Grund</label>
                <input type="text" class="form-control" id="reason" placeholder="Grund der Abrechnung" name="reason">
            </div>
            <div class="form-group">
                <label for="date">Datum</label>
                <input type="date" class="form-control" id="reason" placeholder="Datum" name="date">
            </div>
            <div class="form-group">
                <label for="author">Abrechner</label>
                <input type="text" class="form-control" id="author" placeholder="Abrechner" name="author">
            </div>      
            <div class="form-group">
                <label for="iban">IBAN (falls notwendig)</label>
                <input type="text" class="form-control" id="iban" placeholder="IBAN" name="iban">
            </div>

            <invoice-positions></invoice-positions>
        </form>
    </div>
@endsection
