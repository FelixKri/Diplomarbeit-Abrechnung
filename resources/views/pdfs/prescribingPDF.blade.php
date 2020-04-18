<style>
    table,td{
        border: 1px solid black;
    }

    @page { margin-top: 70px; }
    body { margin-top: 70px; }
</style>

<h1>{{$prescribing->title}}</h1>
<p>Erstellt von: {{$prescribing->author->username}}</p>

<hr>
@if ($prescribing->finished == false)
            <p style="
                    position: absolute;
                    transform: rotate(60deg);
                    color: gray;
                    font-size: 10em;
                    top: 200px;"
            > MUSTER <p>
        @endif
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
    <tr>
        <td style="border: none;">Gesamtsumme:</td>
        <td style="border: none;">{{number_format ($prescribing->total_amount ,2 ,"," ,".")}}€</td>
    </tr>
</table>
<table style="width: 100%;">
    @if ($prescribing->approved == false)
            <p style="
                    position: absolute;
                    transform: rotate(60deg);
                    color: gray;
                    font-size: 10em;
                    top: 200px;"
            > MUSTER <p>
        @endif
    <tr>
        <th>ID</th>
        <th>Klasse</th>
        <th>Nachname</th>
        <th>Vorname</th>
        <th>Betrag [€]</th>
        <th>Anmerkung</th>
    </tr>
    @foreach ($prescribing->positions as $position)
    <tr>
        <td>{{$position->user_id}}</td>
        <td>{{$position->user->group->name}}</td>
        <td>{{$position->user->last_name}}</td>
        <td>{{$position->user->first_name}}</td>
        <td style="text-align: right;">{{number_format ($position->amount ,2 ,"," ,".")}}</td>
        <td>{{$position->annotation}}</td>
    </tr>
    @endforeach
</table>
<script type="text/php">
    if (isset($pdf)) {
        $x = 250;
        $y = 10;
        $text = "Seite {PAGE_NUM} von {PAGE_COUNT}";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);+

        $x = 400;
        $y = 60;
        $text = "__________________________";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);

        $x = 400;
        $y = 70;
        $text = "Unterschrift";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        
    }
</script>
