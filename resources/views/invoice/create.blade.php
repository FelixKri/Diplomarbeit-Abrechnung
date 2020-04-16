@extends('layout.master')

@section('content')
    <invoice-form :reason_list="{{$reasons}}"></invoice-form>
@endsection
