<x-layout>
    @section('custom-css')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @endsection

    @section('title', 'Search Rides')

    @section('heading')
        <div class="flex justify-between">
            <div class="grid grid-cols-9 gap-4">
                <div class="col-span-2">
                    <x-location-input
                        name="departure"
                        label="Departure"
                        id="departure"
                    />
                </div>
                <div class="col-span-2">
                    <x-location-input
                        name="destination"
                        label="Destination"
                        id="destination"
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
                <x-bladewind::button
                    size="medium"
                    radius="full"
                    class="col-span-1 place-self-start drop-shadow-sm hover:drop-shadow-lg"
                >Search
                </x-bladewind::button>
                <div class="mb-3 text-sm text-gray-600 font-medium flex justify-center">
                    <x-bladewind::toggle
                        label="Map toggle"
                        checked="true"
                        onclick="toggleMap()"
                    />
                </div>
            </div>
        </div>
    @endsection

    @section('content')
        <div id="content_wrapper" class="flex h-screen">
            <div id="content_1" class="w-1/2 max-h-screen overflow-auto">
                @foreach($rides as $ride)
                    <a href="{{ route('rides.show', $ride->id) }}" class="block">
                        <x-bladewind::card
                            compact="true"
                            has_shadow="true"
                            hover_effect="true"
                            class="mb-2 mr-2 hover:border-indigo-800"
                        >
                            <x-bladewind::timeline-group
                                position="left"
                                stacked="true"
                                color="pink"
                                anchor="big"
                                completed="true"
                            >
                                <x-bladewind::timeline
                                    date="Departure"
                                    icon="map-pin"
                                    content="{{ $ride->departure_address }}"
                                />
                                <x-bladewind::timeline
                                    date="Destination"
                                    icon="map-pin"
                                    content="{{ $ride->destination_address }}"
                                />
                            </x-bladewind::timeline-group>

                            <hr class="my-2">
                            <div class="flex flex-auto gap-4">
                                <div class="flex flex-auto gap-2">
                                    <x-bladewind::icon name="clock" />
                                    <div class="text-slate-500">
                                        {{ \Carbon\Carbon::parse($ride->departure_datetime)->toDayDateTimeString() }}
                                    </div>
                                </div>
                                <div class="flex flex-auto gap-2">
                                    <x-bladewind::icon name="banknotes" class="text-green-500"/>
                                    <div class="text-slate-500">
                                        RM{{ $ride->price }}
                                    </div>
                                </div>
                                <div class="flex flex-auto gap-2">
                                    <x-bladewind::icon name="users" />
                                    <div class="text-slate-500">
                                        {{ $ride->number_of_passenger }} Seats
                                    </div>
                                </div>
                                <div>
                                    @if($ride->ride_type === 'request')
                                        <x-bladewind::tag label="Ride {{ $ride->ride_type }}" color="orange" rounded="true" class="font-semibold" />
                                    @else
                                        <x-bladewind::tag label="Ride {{ $ride->ride_type }}" color="cyan" rounded="true" class="font-semibold" />
                                    @endif
                                </div>
                            </div>
                        </x-bladewind::card>
                    </a>
                @endforeach
            </div>
            <div id="content_2" class="fixed right-0 w-1/2 h-screen">
                <div id="map" class="h-full"></div>
            </div>
        </div>
    @endsection

    @section('custom-js')
    <script>
        function toggleMap(){
            const content_1 = document.getElementById('content_1');
            const content_2 = document.getElementById('content_2');

            content_1.classList.toggle('w-1/2');
            content_1.classList.toggle('w-full');
            content_1.classList.toggle('mx-40');
            content_1.classList.toggle('overflow-auto');
            content_2.classList.toggle('hidden');
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
    @endsection
</x-layout>
