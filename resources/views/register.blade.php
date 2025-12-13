@extends('layouts.auth')

@section('title', 'Login | Roastly')

@section('content')
    <div class="w-full h-screen flex justify-center items-center">
        <div class="flex flex-col items-center">
            <img src="{{ asset('icon/logoRoastly.png') }}" alt="">
            <h1 class="font-bold text-3xl text-white">Create Account</h1>
             <!-- Form -->
             <form action="{{ route('register') }}" method="POST" class="flex flex-col items-center w-80 mt-5">
                @csrf
                <!-- Email -->
                <div class="w-full mb-4 relative">
                    <input
                        type="text"
                        name="name"
                        id="name"
                        placeholder="Name"
                        class="w-full px-4 py-3 pl-12 rounded-full shadow-md bg-white text-sm text-[#A79277] font-semibold focus:outline-none"
                    >
                    <img src="{{ asset('icon/profile.png') }}" alt="" class="absolute top-3 left-4 w-5 h-5">
                </div>

                <div class="w-full mb-4 relative">
                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="Email"
                        class="w-full px-4 py-3 pl-12 rounded-full shadow-md bg-white text-sm text-[#A79277] font-semibold focus:outline-none"
                    >
                    <img src="{{ asset('icon/google.png') }}" alt="" class="absolute top-3 left-4 w-5 h-5">
                </div>

                <!-- Password -->
                <div class="w-full mb-4 relative">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Password"
                        class="w-full px-4 py-3 pl-12 rounded-full shadow-md bg-white text-sm text-[#A79277] font-semibold focus:outline-none"
                    >
                    <img src="{{ asset('icon/lock-closed.png') }}" alt="" class="absolute top-3 left-4 w-5 h-5">
                </div>

                <!-- Sign Up Button -->
                <button type="submit" class="w-full bg-[#A79277] text-white font-bold py-3 rounded-full shadow-md mb-4">
                    SIGN UP
                </button>
            </form>
        </div>
    </div>
@endsection