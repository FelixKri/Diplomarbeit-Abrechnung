@extends('layout.master')

@section('content')
    <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Titel</th>
            <th scope="col">Ersteller</th>
            <th scope="col">Beschreibung</th>
            <th scope="col">Fällig bis</th>
            <th scope="col">Genehmigt</th>
            <th scope="col">Abgeschlossen</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($presc as $p)
                <tr>
                    <th scope="row">{{$p->id}}</th>
                    <td>{{$p->title}}</td>
                    <td>{{$p->author->username}}</td>
                    <td>{{$p->description}}</td>
                    <td>{{$p->due_until}}</td>
                    <td>{{$p->approved}}</td>
                    <td>{{$p->finished}}</td>
                </tr>
                @foreach ($p->positions as $pos)
                    <p>{{$pos->user_id}}</p>
                @endforeach
            @endforeach
        </tbody>
      </table>
      
@endsection 