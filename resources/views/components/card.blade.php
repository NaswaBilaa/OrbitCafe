<a href="{{ url('/menu/details_order/' . $drink->id) }}" class="cursor-pointer hover:scale-105 transition-transform duration-200 ease-in-out">
    <div class="flex flex-col p-4 shadow-xl rounded-lg w-[13rem] bg-white">
        <div class="flex flex-col items-center">
            <img src="{{ asset('storage/' . $drink->image) }}" alt="{{ $drink->name }}" class="h-32 object-cover rounded mb-2">
            <h3 class="font-semibold text-lg">{{ $drink->name }}</h3>
            <p class="font-semibold text-[#A79277]">Rp {{ number_format($drink->price, 0, ',', '.') }}</p>
        </div>
    </div>
</a>
