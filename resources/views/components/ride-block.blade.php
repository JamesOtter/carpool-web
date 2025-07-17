<div class="mt-2 mb-4">
    <div class="justify-items-center bg-blue-950 border-t-4 border-t-teal-400 rounded-xl shadow-lg p-4">
{{--        <h1 class="text-white pb-2">#Ride {{ $index + 1 }}</h1>--}}
        <div class="flex flex-col items-center text-center mb-1">
            @if($ride['destination'])
                <div class="pb-1">
                    <h1 class="text-indigo-300 text-sm">FROM</h1>
                </div>
                <div>
                    <h1 class="text-white text-lg font-semibold">Home</h1>
                </div>

                <div>
                    <x-bladewind::icon class="text-teal-400 my-3 justify-center" name="arrow-down-circle" />
                </div>

                <div class="pb-1">
                    <h1 class="text-indigo-300 text-sm">TO</h1>
                </div>
                <div class="mb-2">
                    <h1 class="text-white text-lg font-semibold">{{ $ride['destination'] }}</h1>
                    <input type="text" value="{{ $ride['destination_address'] }}" name="destination_address_{{ $index + 1 }}" hidden>
                    <input type="text" value="{{ $ride['destination_id'] }}" name="destination_id_{{ $index + 1 }}" hidden>
                </div>
            @else
                <div class="pb-1">
                    <h1 class="text-indigo-300 text-sm">FROM</h1>
                </div>
                <div>
                    <h1 class="text-white text-lg font-semibold">{{ $ride['departure'] }}</h1>
                    <input type="text" value="{{ $ride['departure_address'] }}" name="departure_address_{{ $index + 1 }}" hidden>
                    <input type="text" value="{{ $ride['departure_id'] }}" name="departure_id_{{ $index + 1 }}" hidden>
                </div>

                <div>
                    <x-bladewind::icon class="text-teal-400 my-3 justify-center" name="arrow-down-circle" />
                </div>

                <div class="pb-1">
                    <h1 class="text-indigo-300 text-sm">TO</h1>
                </div>
                <div class="mb-2">
                    <h1 class="text-white text-lg font-semibold">Home</h1>
                </div>
            @endif
        </div>

        <hr class="w-full border-t-1 border-gray-500">

        <div class="bg-white/10 backdrop-blur-sm rounded-lg justify-items-center p-2 my-4 w-full border border-gray-500">
            <div>
                <h1 class="text-teal-500 pb-1 text-sm font-medium">DAY</h1>
            </div>
            <div>
                <h1 class="font-bold text-white">{{ $ride['day'] }}</h1>
                <input type="text" value="{{ $ride['day'] }}" name="day_{{ $index + 1 }}" hidden>
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-sm rounded-lg justify-items-center p-2 w-full border border-gray-500">
            <div>
                <h1 class="text-teal-500 pb-1 text-sm font-medium">TIME</h1>
            </div>
            <div>
                <h1 class="font-bold text-white">{{ \Carbon\Carbon::createFromFormat('H:i', $ride['departure_time'])->format('h:i A') }}</h1>
                <input type="time" value="{{ $ride['departure_time'] }}" name="departure_time_{{ $index + 1 }}" hidden>
            </div>
        </div>
    </div>
</div>
