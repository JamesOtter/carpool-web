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

    <h1>Welcome to Carpool app.</h1>

</x-layout>
