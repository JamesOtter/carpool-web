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
        Home
    </x:slot:title>

    <x:slot:heading>
        Home
    </x:slot:heading>

    <div id="map"></div>

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

{{--    <h1>Welcome to Carpool app.</h1>--}}
{{--    <br>--}}

{{--    <ul>--}}
{{--        @foreach($locations as $location)--}}
{{--            <li>--}}
{{--                From: {{ $location['from_address'] }}--}}
{{--            </li>--}}
{{--        @endforeach--}}
{{--    </ul>--}}
</x-layout>
