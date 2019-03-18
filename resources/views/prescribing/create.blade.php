@extends('layout.master')

@section('content')
    <div class="container">
            <h1>Neue Vorschreibung erstellen</h1>

            <label for="author">Vorschreiber: </label> <input type="text" value="Franz Saler" disabled name="author" class="form-control">
            <label for="date">Datum der Vorschreibung: </label> <input type="date" name="date" id="" value="2018-07-22" disabled class="form-control">
            <label for="due_until">Spätestens gewünschtes Einzahlungsdatum: </label> <input type="date" name="due_until" class="form-control">
            <label for="reason">Grund der Vorschreibung: </label> <input type="text" name="reason" class="form-control">
            <label for="total_amount">Gesamtsumme der Vorschreibung: </label> <input type="number" name="total_amount" id="" class="form-control"> 
            <hr>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addClass">Klasse(n) hinzufügen</button> 
            <button class="btn btn-primary" data-toggle="modal" data-target="#addUser">Person(n) hinzufügen</button>
        
            <add-class-modal></add-class-modal>
            <add-person-modal></add-person-modal>
            
            <student-list-table></student-list-table>
            
    </div>
    
    
@endsection