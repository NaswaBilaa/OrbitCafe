@extends('layouts.auth')

@section('title', 'Login | Roastly')

@section('content')
    <div class="w-full h-screen flex justify-center items-center">
        <div class="flex flex-col items-center">
            <img src="{{ asset('icon/logoRoastly.png') }}" alt="">
            <h1 class="font-bold text-3xl text-white">Welcome Back!</h1>

            @if (isset($already_logged_in) && $already_logged_in)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded-md w-80 text-center mt-5">
                    <p>You are already logged in as <strong>{{ $user_name }}</strong>.</p>
                    <p>You need to log out before logging in as a different user.</p>

                    <div class="flex justify-between mt-4">
                        <a href="{{ route('all') }}" class="px-4 py-2 bg-gray-300 rounded-full">Cancel</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-full">Logout</button>
                        </form>
                    </div>
                </div>
            @endif
            
           @if (!isset($already_logged_in) || !$already_logged_in)
             <!-- Form -->
             <form action="{{ route('login') }}" method="POST" class="flex flex-col items-center w-80 mt-5">
                @csrf
                <!-- Email -->
                <div class="w-full mb-4 relative">
                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="Username or email"
                        class="w-full px-4 py-3 pl-12 rounded-full shadow-md bg-white text-sm text-[#A79277] font-semibold focus:outline-none"
                    >
                    <img src="{{ asset('icon/profile.png') }}" alt="" class="absolute top-3 left-4 w-5 h-5">
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

                <!-- Login Button -->
                <button type="submit" class="w-full bg-[#A79277] text-white font-bold py-3 rounded-full shadow-md mb-4">
                    LOGIN
                </button>

                <!-- Forgot Link -->
                <a href="#" class="text-sm text-white underline mb-2 no-underline">Forgot Username or Password?</a>

                <!-- Create Account Link -->
                <a href="{{ route('register') }}" class="text-md tracking-wide text-white mt-10">Create new account</a>
            </form>
            @endif
        </div>
    </div>
@endsection