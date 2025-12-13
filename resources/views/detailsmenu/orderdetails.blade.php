@extends('layouts.detailsOrder')

@section('content')
    @section('nameMenu', $menu->name)
    @section('price', 'Rp ' . number_format($menu->price, 0, ',', '.'))
    @section('image')
        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full">
    @endsection
    @section('description', $menu->description)
@endsection
