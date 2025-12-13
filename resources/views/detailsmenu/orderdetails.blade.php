@extends('layouts.detailsOrder')

@section('content')
    @section('nameMenu', $drink->name)
    @section('price', 'Rp ' . number_format($drink->price, 0, ',', '.'))
    @section('image')
        <img src="{{ asset('storage/' . $drink->image) }}" alt="{{ $drink->name }}" class="w-full">
    @endsection
    @section('description', $drink->description)
@endsection
