@extends('layouts.menu')

@section('content')
    @forelse ($drinks as $drink)
        @include('components.card', ['drink' => $drink])
    @empty
        <p class="text-gray-500">Tidak ada minuman ditemukan.</p>
    @endforelse
@endsection
