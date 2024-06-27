@extends('layouts.app')

@section('content')
    <message-list :channel="{{ $channel }}"></message-list>
@endsection
