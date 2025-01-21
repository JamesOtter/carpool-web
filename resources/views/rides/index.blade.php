<x-layout>
    <x:slot:header>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <style>
            #map {
                height: 500px;
                width: 100%;
                position: sticky;
                top: 100px;
            }
        </style>
    </x:slot:header>

    <x:slot:title>
        Rides
    </x:slot:title>

    <x:slot:heading>
        Rides
    </x:slot:heading>

    <div class="text-gray-600 flex justify-end pb-6">
        <x-bladewind::toggle
            label="Map toggle"
            onclick="toggleMap()"
        />
    </div>

    <div id="contentWrapper" class="grid grid-cols-2">
        <div id="content_1" class="flex flex-wrap basis-1/2">
            @foreach($rides as $ride)
                <p>No.{{ $ride->id  }} address: {{ $ride->departure_address }}</p>
            @endforeach
        </div>
        <div id="content_2" class="flex basis-1/2">
            <div id="map"></div>
        </div>
    </div>

    <script>
        function toggleMap(){
            const content_2 = document.getElementById('content_2');
            const contentWrapper = document.getElementById('contentWrapper');

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
