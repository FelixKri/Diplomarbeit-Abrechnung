@extends('layout.master')

@section('content')
    <invoice-detail :id="{{$id}}" :reason_list="{{$reasons}}"></invoice-detail>
@endsection