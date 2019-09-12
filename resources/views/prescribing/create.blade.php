@extends('layout.master')

@section('content')
    <div class="container">
        <form action="/prescribing/new" method="post">
            {{ csrf_field() }}
            <h1>Neue Vorschreibung erstellen</h1>

            <label for="title">Titel: </label> <input type="text" name="title" class="form-control" id="">
            <label for="author">Vorschreiber: </label> <input type="text" value="admin" readonly name="author" class="form-control">
            <label for="date">Datum der Vorschreibung: </label> <input type="date" name="date" id="" value="2018-07-22" disabled class="form-control">
            <label for="due_until">Spätestens gewünschtes Einzahlungsdatum: </label> <input type="date" name="due_until" class="form-control">
            <label for="reason_suggestion">Grundvorschlag: </label> <input type="text" name="reason_suggestion" class="form-control">
            <label for="reason">Grund</label> 
            <select name="reason" id="" class="form-control">
                <option value="0">0</option>
                @foreach($reasons as $reason)
                    <option value="{{ $reason->title }}">{{ $reason->title }}</option>
                @endforeach
            </select>
            <label for="description">Beschreibung: </label> <input type="text" name="description" id="" class="form-control"> 
            <hr>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addUser" type=button>Hinzufügen</button>
        
            <add-person-modal v-on:addstudents="addStudents()"></add-person-modal>
            
            <student-list-table></student-list-table>

            <input type="submit" value="Speichern" class="btn btn-success">
            @if ($errors->any())
                {!! implode('', $errors->all('<div>:message</div>')) !!}
            @endif
        </form>
    </div>
    
    
@endsection