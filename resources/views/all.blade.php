@extends('layouts.menu')

@section('content')
    @forelse ($menus as $menu)
        @include('components.card', ['menu' => $menu])
    @empty
        <p class="text-gray-500">Tidak ada minuman ditemukan.</p>
    @endforelse
@endsection
