<x-layout>
    <x:slot:header>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    </x:slot:header>

    <x:slot:title>
        Search Rides
    </x:slot:title>

    <x:slot:heading>
        <div class="flex justify-between">
            <div class="grid grid-cols-9 gap-4">
                <div class="col-span-2">
                    <x-bladewind::input
                        name="departure"
                        label="Departure"
                        prefix="map-pin"
                        prefix_is_icon="true"
                    />
                </div>
                <div class="col-span-2">
                    <x-bladewind::input
                        name="destination"
                        label="Destination"
                        prefix="map-pin"
                        prefix_is_icon="true"
                    />
                </div>

                <x-bladewind::datepicker
                    min_date="{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}"
                    label="Select a date"
                />
                @php
                    $ride_type = [
                        [ 'label' => 'Request', 'value' => 'request' ],
                        [ 'label' => 'Offer', 'value' => 'offer' ],
                    ];
                @endphp
                <x-bladewind::select
                    name="ride_type"
                    label="Ride type"
                    :data="$ride_type"
                />
                <x-bladewind::input
                    numeric="true"
                    label="No. of Passenger"
                />
                <x-bladewind::button size="medium" radius="full" class="col-span-1 place-self-start">Search</x-bladewind::button>
                <div class="mb-3 text-sm text-gray-600 font-medium flex justify-center">
                    <x-bladewind::toggle
                        label="Map toggle"
                        checked="true"
                        onclick="toggleMap()"
                    />
                </div>
            </div>
        </div>
    </x:slot:heading>

    <div class="flex h-screen">
        <div class="w-1/2 max-h-screen overflow-auto">
            @foreach($rides as $ride)
                <x-bladewind::card
                    compact="true"
                    has_shadow="true"
                    hover_effect="true"
                    class="mb-2 mr-2"
                >
                    Username: {{ $ride->user->name }}
                    <br>
                    Ride type: {{ $ride->ride_type }}
                    <br>
                    No: {{ $ride->id }}
                    <br>
                    Address: {{ $ride->departure_address }}
                    <br>
                    Price: RM {{ $ride->price }}
                </x-bladewind::card>
            @endforeach
        </div>
        <div class="fixed right-0 w-1/2 h-screen">
            <div id="map" class="h-full"></div>
        </div>
    </div>

{{--    <div id="contentWrapper" class="grid grid-cols-2">--}}
{{--        <div id="content_1" class="flex flex-wrap basis-1/2">--}}
{{--            @foreach($rides as $ride)--}}
{{--                <p>No.{{ $ride->id  }} address: {{ $ride->departure_address }}</p>--}}
{{--            @endforeach--}}
{{--        </div>--}}
{{--        <div id="content_2" class="flex basis-1/2">--}}
{{--            <div id="map"></div>--}}
{{--        </div>--}}
{{--    </div>--}}

    <script>
        function toggleMap(){
            const content_1 = document.getElementById('content_1');
            const content_2 = document.getElementById('content_2');
            const contentWrapper = document.getElementById('contentWrapper');

            content_1.classList.toggle('px-32');
            content_2.classList.toggle('hidden');
            contentWrapper.classList.toggle('grid-cols-1');
            contentWrapper.classList.toggle('grid-cols-2');
        }

        var map = L.map('map').setView([4.3348, 101.1351], 15); //Default location and zoom

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        L.marker([4.3348, 101.1351]).addTo(map)
            .bindPopup('A pretty CSS popup.<br> Easily customizable.')
            .openPopup();

    </script>

</x-layout>
