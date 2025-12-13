<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Menu | Roastly</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="pb-2 bg-[#FEFDF8]">
    <header class="w-full text-center sticky top-0 left-0 pt-8 pb-2 bg-[#FEFDF8] relative">
        <div class="absolute top-4 right-6 flex gap-2">
            <!-- Tombol Cart -->
            <a href="{{ route('cart.index') }}"
            class="py-2 px-8 shadow-md rounded-lg text-md font-semibold transition hover:bg-[#402F0B] hover:text-white bg-white text-[#402F0B]">
                Cart
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600">
                    Logout
                </button>
            </form>
        </div>
        
        <h1 class="text-xl font-bold text-[#B97D0E]">OUR MENU</h1>
        <h1 class="text-4xl font-bold text-[#402F0B]">Find Your Favorite Cup</h1>
        @php
            $menus = [
                'All' => 'menu',
                'Signature' => 'menu/signature',
                'Coffee' => 'menu/coffee',
                'Milk Based' => 'menu/milk based',
                'Frappe' => 'menu/frappe',
                'Dessert' => 'menu/dessert',
                'Tea' => 'menu/tea',
            ];
        @endphp

        <nav class="pt-10 px-2 md:px-12">
            <div class="flex lg:hidden mb-4">
                <div id="button-menu" class="shadow-lg rounded-lg px-3 py-2 flex items-center gap-x-2 bg-[#402F0B]">
                    <span class="p-2 bg-white rounded-full">
                        <img src="{{ asset('icon/widget.png') }}" alt="" class="w-6 h-6">
                    </span>
                    <p class="font-semibold text-white">Show Menu</p>
                </div>
            </div>
            <ul class="flex flex-wrap items-center justify-center gap-x-4 gap-y-6">
                @foreach ($menus as $label => $url)
                    <li class="hidden lg:flex showMenu">
                        <a href="/{{ $url }}"
                            class="py-2 px-8 shadow-md rounded-lg text-md font-semibold transition hover:bg-[#402F0B] hover:text-white {{ request()->is($url) ? 'bg-[#402F0B] text-white' : 'bg-white text-[#402F0B]' }}">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </header>
    <main class="flex items-center justify-center mt-8 w-full">
        <div class="grid grid-cols-2 md:flex md:flex-wrap  items-center justify-center lg:w-3/4 gap-4">
            @yield('content')
        </div>
    </main>
</body>

</html>
