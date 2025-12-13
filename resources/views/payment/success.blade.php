<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Success Payment | Roastly</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FEFDF8]">
    <main class="flex items-center justify-center w-full min-h-screen">
        <div class="w-1/2 bg-white rounded-xl px-16 py-8 shadow-md flex flex-col text-center mb-10">
            <h1 class="text-4xl font-bold text-[#402F0B] mb-4">Thank You!</h1>
            <h1 class="text-xl font-bold text-[#B97D0E] mb-6">
                Our Barista will Serve the Best Coffee in Town, Stay Tune ;)
            </h1>

            {{-- Go Home Button --}}
            <a href="{{ route('all') }}"
               class="py-2 px-8 shadow-md rounded-lg text-md font-semibold transition bg-[#402F0B] text-white hover:bg-white hover:text-[#402F0B] border border-[#402F0B]">
                Go Home
            </a>
        </div>
    </main>
</body>
</html>
