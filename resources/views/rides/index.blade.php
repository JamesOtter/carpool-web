<x-layout>
    <x:slot:header>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <style>
            #map {
                height: 500px;
                width: 100%;
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
        />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2">
        <div class="flex justify-center">
            <h1>Content 1</h1>
        </div>
        <div class="flex justify-center">
            <h1>Content 2</h1>
        </div>
    </div>

    <br><br>
    <div id="map"></div>

    @foreach($rides as $ride)
        <p>No.{{ $ride->id  }} address: {{ $ride->departure_address }}</p>
    @endforeach

    <script>
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
