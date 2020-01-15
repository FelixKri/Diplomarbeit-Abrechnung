<style>
    table,td{
        border: 1px solid black;
    }
</style>

<h1>{{$prescribing->title}}</h1>
<p>Erstellt von: {{$prescribing->author->username}}</p>
<hr>
<table style="width: 90%; border: none;">
    <tr>
        <td style="border: none;">Datum der Erstellung:</td>
        <td style="border: none;">{{date('d.m.Y', strtotime($prescribing->date))}}</td>
    </tr>
    <tr>
        <td style="border: none;">Spätest gewünschtes Einzahlungsdatum:</td>
        <td style="border: none;">{{date('d.m.Y', strtotime($prescribing->due_until))}}</td>
    </tr>
    <tr>
        <td style="border: none;">Grund:</td>
        @if ($prescribing->reason_suggestion)
            <td style="border: none;">{{$prescribing->reason_suggestion}}</td>
        @else
            <td style="border: none;">{{$prescribing->reason->title}}</td>    
        @endif
    </tr>
    <tr>
        <td style="border: none;">Beschreibung/Anmerkung: </td>
        <td style="border: none;">{{$prescribing->description}}</td>
    </tr>
</table>
<table style="width: 100%;">
    <tr>
        <th>ID</th>
        <th>Klasse</th>
        <th>Nachname</th>
        <th>Vorname</th>
        <th>Betrag</th>
        <th>Anmerkung</th>
    </tr>
    @foreach ($prescribing->positions as $position)
    <tr>
        <td>{{$position->user_id}}</td>
        <td>{{$position->user->group->name}}</td>
        <td>{{$position->user->last_name}}</td>
        <td>{{$position->user->first_name}}</td>
        <td>{{$position->amount}}€</td>
        <td>{{$position->annotation}}</td>
    </tr>
    @endforeach
</table>
