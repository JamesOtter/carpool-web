<x-layout>
    @section('custom-css')

    @endsection

    @section('title', 'Search Rides')

    @section('heading')
        <div class="flex justify-between">
            <form method="GET" action="/rides" class="grid grid-cols-9 gap-4">
                <div class="col-span-2">
                    <x-location-input
                        name="departure"
                        label="Departure"
                        id="departure"
                        value="{{ request('departure') }}"
                    />
                </div>
                <div class="col-span-2">
                    <x-location-input
                        name="destination"
                        label="Destination"
                        id="destination"
                        value="{{ request('destination') }}"
                    />
                </div>

                <x-bladewind::datepicker
                    min_date="{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}"
                    label="Select a date"
                    name="date"
                    value="{{ request('date') }}"
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
                    value="{{ request('ride_type') }}"
                />
                <x-bladewind::button
                    size="medium"
                    radius="full"
                    class="col-span-1 place-self-start drop-shadow-sm hover:drop-shadow-lg"
                    can_submit="true"
                >
                    Filter
                </x-bladewind::button>

                <div>
                    <button
                        class="text-sm font-normal text-white bg-blue-900 rounded-xl px-4 py-2 hover:bg-blue-950 hover:border hover:border-blue-800"
                        onclick="showModal('large-modal')"
                    >
                        Use My Timetable
                    </button>
                    <x-bladewind::modal
                        size="large"
                        title="Large Modal"
                        name="large-modal"
                        blur_size="small"
                        show_close_icon="true"
                    >
                        I am the large modal. If I am not large enough to contain
                        your needs, check out my xl brother.
                    </x-bladewind::modal>
                </div>


                <div class="mb-3 text-sm text-gray-600 font-medium flex content-end">
                    <x-bladewind::toggle
                        label="Map toggle"
                        checked="true"
                        onclick="toggleMap()"
                    />
                </div>
            </form>
        </div>
    @endsection

    @section('content')
        <div id="content_wrapper" class="flex h-screen">
            <div id="content_1" class="w-1/2 max-h-screen overflow-auto">
                @if($sortedRides->count())
                    @foreach($sortedRides as $rideData)
                        @if($rideData['type'] === 'regular')
                            <a href="{{ route('rides.show', $rideData['ride']->id) }}" class="block">
                        @else
                            <a href="{{ route('recurring-rides.show', $rideData['ride']->recurringRide->id) }}" class="block">
                        @endif
{{--                        <a href="{{ route('rides.show', $rideData['ride']->id) }}" class="block">--}}
                            <x-bladewind::card
                                compact="true"
                                has_shadow="true"
                                hover_effect="true"
                                class="mb-2 mr-2 hover:border-indigo-800"
                            >
                                <x-bladewind::timelines
                                    position="left"
                                    stacked="true"
                                    color="pink"
                                    anchor="big"
                                    completed="true"
                                >
                                    <x-bladewind::timeline
                                        date="Departure"
                                        icon="map-pin"
                                        content="{{ $rideData['ride']->departure_address }}"
                                    />
                                    <x-bladewind::timeline
                                        date="Destination"
                                        icon="flag"
                                        content="{{ $rideData['ride']->destination_address }}"
                                    />
                                </x-bladewind::timelines>
                                <hr class="my-2">
                                <div class="flex flex-auto gap-4">
                                    <div class="flex flex-auto gap-2">
                                        <x-bladewind::icon name="clock" />
                                        @if($rideData['type'] === 'regular')
                                            <div class="text-slate-500">
                                                {{ \Carbon\Carbon::parse($rideData['ride']->departure_date)->format('D, M j, Y') }}
                                                {{ \Carbon\Carbon::parse($rideData['ride']->departure_time)->format('h:i A') }}
                                            </div>
                                        @else
                                            <div class="text-slate-500">
                                                <p>Start date: {{ \Carbon\Carbon::parse($rideData['ride']->recurringRide->start_date)->format('D, M j, Y') }}</p>
                                                <p>End date: {{ \Carbon\Carbon::parse($rideData['ride']->recurringRide->end_date)->format('D, M j, Y') }}</p>
                                                <p>Time: {{ \Carbon\Carbon::parse($rideData['ride']->departure_time)->format('h:i A') }}</p>
                                            </div>
                                        @endif

                                    </div>
                                    <div class="flex flex-auto gap-2">
                                        <x-bladewind::icon name="banknotes" class="text-green-500"/>
                                        <div class="text-slate-500 grid-rows-3">
                                            <p>Base Price: RM <span id="base-price-{{ $rideData['ride']->id }}">{{ $rideData['ride']->price }}</span></p>
                                            <p>Surge Price: RM <span id="surge-price-{{ $rideData['ride']->id }}">0.00</span></p>
                                            <p>Total Price: RM <span id="total-price-{{ $rideData['ride']->id }}">{{ $rideData['ride']->price }}</span></p>
                                        </div>
                                    </div>
                                    <div class="flex flex-auto gap-2">
                                        <x-bladewind::icon name="users" />
                                        <div class="text-slate-500">
                                            {{ $rideData['ride']->number_of_passenger }} Seats
                                        </div>
                                    </div>
                                    <div>
                                        <div>
                                            @if($rideData['ride']->ride_type === 'request')
                                                <x-bladewind::tag label="Ride {{ $rideData['ride']->ride_type }}" color="orange" rounded="true" class="font-semibold" />
                                            @else
                                                <x-bladewind::tag label="Ride {{ $rideData['ride']->ride_type }}" color="cyan" rounded="true" class="font-semibold" />
                                            @endif
                                        </div>
                                        <div>
                                            @if($rideData['type'] === 'recurring')
                                                <x-bladewind::tag label="Recurring" color="purple" rounded="true" class="font-semibold" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </x-bladewind::card>
                        </a>
                    @endforeach
                    <!-- This is the pagination navigation links -->
{{--                    <div class="mr-2">--}}
{{--                        {{ $rides->appends(request()->query())->links() }}--}}
{{--                    </div>--}}
                @else
                    <x-bladewind::empty-state
                        message="There are no rides available"
                        heading="Post Ride Now"
                        button_label="Post Ride"
                        onclick="window.location.href='/rides/create'"
                    >
                    </x-bladewind::empty-state>
                @endif

            </div>

            <div id="content_2" class="right-0 w-1/2 h-screen">
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
    </script>

    <script>
        function initMap() {
            let defaultLocation = { lat: 4.3348, lng: 101.1351 }; // Default location UTAR

            let map = new google.maps.Map(document.getElementById("map"), {
                center: defaultLocation,
                zoom: 15,
                mapTypeControl: false,
                streetViewControl: false,
                mapId: "7c985f4c6ade2d49931dbd91",
            });

            // Try to get user's location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        let userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        map.setCenter(userLocation);
                    },
                    () => {
                        console.log("Geolocation not allowed, using default location.");
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "top",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                        Toast.fire({
                            icon: "info",
                            title: "Geolocation not allowed, using default location."
                        });
                    }
                );
            }

            let sortedRides = Object.values({!! json_encode($sortedRides, JSON_HEX_TAG) !!});

            sortedRides.forEach((rideData,i) => {

                (async () => {

                    const place = new google.maps.places.Place({
                        id: rideData.ride.departure_id,
                        requestedLanguage: "en", // optional
                    });

                    await place.fetchFields({
                        fields: ["displayName", "formattedAddress", "location"],
                    });

                    if (place && place.location) {
                        let marker = new google.maps.marker.AdvancedMarkerElement({
                            map,
                            position: place.location,
                            title: rideData.ride.id.toString(),
                        });

                        marker.addListener("click", () => {
                            let rideUrl = rideData.type === "recurring"
                                ? `/recurring-rides/${rideData.ride.recurring_id}`
                                : `/rides/${rideData.ride.id}`;

                            let content = `
                                            <div style="min-width: 300px; min-height: 180px; padding: 5px;">
                                                <h3><strong>From</strong></h3>
                                                <p>${rideData.ride.departure_address}</p>
                                                <br>
                                                <h3><strong>To</strong></h3>
                                                <p>${rideData.ride.destination_address}</p>
                                                <br>
                                                <a href="${rideUrl}"
                                                   style="display: inline-block; padding: 6px 10px; background: #007bff; color: white; font-weight: bold; border-radius: 4px; text-decoration: none; text-align: center;">
                                                    View Details
                                                </a>
                                            </div>
                                        `;

                            let infoWindow = new google.maps.InfoWindow({
                                content: content,
                                maxWidth: 350,
                            });
                            infoWindow.open(map, marker);
                        });
                    }

                })();

                // let service = new google.maps.places.PlacesService(map);
                //
                // service.getDetails({ placeId: rideData.ride.departure_id }, (result, status) => {
                //
                //     if (status === google.maps.places.PlacesServiceStatus.OK) {
                //         let marker = new google.maps.Marker({
                //             map,
                //             position: result.geometry.location,
                //             title: rideData.ride.id,
                //         });
                //
                //         marker.addListener("click", () => {
                //             let rideUrl = rideData.type === "recurring"
                //                 ? `/recurring-rides/${rideData.ride.recurring_id}`
                //                 : `/rides/${rideData.ride.id}`;
                //
                //             let content = `
                //                 <div style="min-width: 300px; min-height: 180px; padding: 5px;">
                //                     <h3><strong>From</strong></h3>
                //                     <p>${rideData.ride.departure_address}</p>
                //                     <br>
                //                     <h3><strong>To</strong></h3>
                //                     <p>${rideData.ride.destination_address}</p>
                //                     <br>
                //                     <a href="${rideUrl}"
                //                        style="display: inline-block; padding: 6px 10px; background: #007bff; color: white; font-weight: bold; border-radius: 4px; text-decoration: none; text-align: center;">
                //                         View Details
                //                     </a>
                //                 </div>
                //             `;
                //
                //             let infoWindow = new google.maps.InfoWindow({
                //                 content: content,
                //                 maxWidth: 350, // Adjust width to prevent wrapping
                //             });
                //             infoWindow.open(map, marker);
                //         });
                //     }
                // });
            });
        }

        window.onload = initMap;
    </script>

{{--    <script>--}}
{{--        var map = L.map('map').setView([4.3348, 101.1351], 15); //Default location and zoom--}}

{{--        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {--}}
{{--            maxZoom: 19,--}}
{{--            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'--}}
{{--        }).addTo(map);--}}

{{--        L.marker([4.3348, 101.1351]).addTo(map)--}}
{{--            .bindPopup('A pretty CSS popup.<br> Easily customizable.')--}}
{{--            .openPopup();--}}

{{--    </script>--}}

    <script>
        function updatePrice(rideId) {
            $.ajax({
                url: "/get-price",
                method: 'POST',
                data: {
                    ride_id: rideId,
                    _token: '{{ csrf_token() }}' // Laravel CSRF token
                },
                success: function(response) {
                    // Update the price on the page
                    $('#base-price-' + rideId).text(response.base_price.toFixed(2));
                    $('#surge-price-' + rideId).text(response.surge_price.toFixed(2));
                    $('#total-price-' + rideId).text(response.total_price.toFixed(2));
                },
                error: function(xhr) {
                    console.error('Error fetching price:', xhr.responseText);
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    Toast.fire({
                        icon: "error",
                        title: "Failed to fetch price"
                    });
                }
            });
        }

        //Update prices on page load
        $(document).ready(function() {
            @foreach($sortedRides as $rideData)
                updatePrice({{ $rideData['ride']->id }});
            @endforeach

            const Toast = Swal.mixin({
                toast: true,
                position: "top",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: "Price Refreshed"
            });
        });

        // Update prices every 60 seconds (optional)
        {{--setInterval(function() {--}}
        {{--    @foreach($rides as $ride)--}}
        {{--    updatePrice({{ $ride->id }});--}}
        {{--    @endforeach--}}

        {{--    const Toast = Swal.mixin({--}}
        {{--        toast: true,--}}
        {{--        position: "top",--}}
        {{--        showConfirmButton: false,--}}
        {{--        timer: 3000,--}}
        {{--        timerProgressBar: true,--}}
        {{--        didOpen: (toast) => {--}}
        {{--            toast.onmouseenter = Swal.stopTimer;--}}
        {{--            toast.onmouseleave = Swal.resumeTimer;--}}
        {{--        }--}}
        {{--    });--}}
        {{--    Toast.fire({--}}
        {{--        icon: "success",--}}
        {{--        title: "Price Refreshed"--}}
        {{--    });--}}
        {{--}, 60000); // 60 seconds--}}
    </script>
    @endsection
</x-layout>
