@extends('newchat')
@section('content')
@foreach($records as $record)
<p>{{$record['message']}}</p>
@endforeach
@endsection