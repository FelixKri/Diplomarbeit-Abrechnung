<style>
    table,td{
        border: 1px solid black;
    }
</style>

<h1>{{$invoice->reason}}</h1>
<p>Ursprünglicher Author: {{$invoice->author->username}}</p>
<hr>
<table style="width: 90%; border: none;">
    <tr>
        <td style="border: none;">Datum der Erstellung:</td>
        <td style="border: none;">{{date('d.m.Y', strtotime($invoice->date))}}</td>
    </tr>
    <tr>
        <td style="border: none;">Grund:</td>
        <td style="border: none;">{{$invoice->reason}}</td>
    </tr>
    <tr>
        <td style="border: none;">Beschreibung/Anmerkung: </td>
        <td style="border: none;">{{$invoice->annotation}}</td>
    </tr>
    <tr>
        <td style="border: none; font-weight: bold;">Gesamtbetrag:</td>
        <td style="border: none; font-weight: bold;">{{$invoice->total_amount}}</td>
    </tr>
</table>

@foreach ($invoice->positions as $position)
    <h2>{{$position->name}}</h2>
    <p style="font-weight: bold;">Gesamtbetrag der Position: {{$position->total_amount}}</p>
    @if ($position->paid_by_teacher)
    <p>Position wurde von Lehrpersonal/Abrechner bezahlt, überweisen auf IBAN: {{$position->iban}}</p>
    @endif
    <p>Anmerkungen zur Position:</p>
    <p style="font-family: 'Courier New', Courier, monospace">Anmerkungen müssen erst gespeichert werden damit sie hier angezeigt werden können</p>
    <table style="width: 100%;">
        <tr>
            <th>ID</th>
            <th>Klasse</th>
            <th>Nachname</th>
            <th>Vorname</th>
            <th>Betrag</th>
        </tr>
        @foreach ($position->userHasInvoicePosition as $student)
        <tr>
            <td>{{$student->user_id}}</td>
            <td>{{$student->user->group->name}}</td>
            <td>{{$student->user->last_name}}</td>
            <td>{{$student->user->first_name}}</td>
            <td>{{$student->amount}}€</td>
        </tr>
        @endforeach
    </table>
    
@endforeach