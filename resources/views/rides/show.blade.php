<x-layout>
    @section('custom-css')
    @endsection

    @section('title', 'Details Page')

    @section('heading')
            <div class="pb-3 md:px-32">
                <x-breadcrumbs :breadcrumbs="[
                    ['name' => 'Rides', 'link' => '/rides'],
                    ['name' => 'Details', 'link' => null],
                ]"/>
            </div>
    @endsection

    @section('content')
        <div class="md:px-32">
            <div class="flex gap-5 mb-4 mt-2">
                <div class="flex gap-4 bg-white border-2 border-gray-200 rounded-md p-4 text-blue-950 text-2xl font-serif tracking-wider shadow-sm">
                    <div>
                        <x-bladewind::icon name="calendar" />
                    </div>
                    <div class="pt-1 font-bold">
                        {{ \Carbon\Carbon::parse($ride->departure_date)->format('l, M j, Y') }}
                    </div>
                </div>
                <div class="flex gap-4 bg-white border-2 border-gray-200 rounded-md p-4 text-blue-950 text-2xl font-serif tracking-wider shadow-sm">
                    <div>
                        <x-bladewind::icon name="clock" />
                    </div>
                    <div class="font-bold">
                        {{ \Carbon\Carbon::parse($ride->departure_time)->format('h:i A') }}
                    </div>
                </div>
            </div>

            <div id="map" style="width: 100%; height: 500px;"></div>

            <div class="grid grid-cols-10 gap-4 mt-5">
                <div class="grid col-span-7 gap-3 border-r-2 border-gray-300 pr-5">
                    <x-bladewind::card compact="true">
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
                                icon="flag"
                                content="{{ $ride->destination_address }}"
                            />
                        </x-bladewind::timeline-group>
                    </x-bladewind::card>

                    <div class="flex gap-5">
                        <x-bladewind::card compact="true" class="flex-auto">
                            <div class="mx-2">
                                <div class="text-5xl text-center font-bold text-blue-950 py-6">
                                    {{ $ride->number_of_passenger }}
                                </div>

                                <div class="flex gap-2 text-gray-500 mb-3 text-center place-content-center">
                                    <x-bladewind::icon name="users" />
                                    <p class="font-light">Number of passenger(s)</p>
                                </div>
                            </div>
                        </x-bladewind::card>

                        <x-bladewind::card compact="true" class="flex-auto">
                            <div class="mx-2">
                                <div class="flex place-content-center text-blue-950 py-6 gap-3">
                                    <div class="text-5xl font-bold">
                                        {{ $ride->distance }}
                                    </div>
                                    <div class="place-content-end text-lg">
                                        km
                                    </div>
                                </div>

                                <div class="flex gap-2 text-gray-500 mb-3 place-content-center">
                                    <p class="font-light">Distance</p>
                                </div>
                            </div>
                        </x-bladewind::card>

                        <x-bladewind::card compact="true" class="flex-auto">
                            <div class="mx-2">
                                <div class="flex place-content-center text-blue-950 py-6 gap-3">
                                    <div class="text-5xl font-bold">
                                        {{ $ride->duration }}
                                    </div>
                                    <div class="place-content-end text-lg">
                                        min(s)
                                    </div>
                                </div>

                                <div class="flex gap-2 text-gray-500 mb-3 place-content-center">
                                    <p class="font-light">Estimated duration</p>
                                </div>
                            </div>
                        </x-bladewind::card>
                    </div>

                    <x-bladewind::card compact="true">
                        <div class="ml-2">
                            <div class="flex gap-2 text-gray-500 mb-3">
                                <x-bladewind::icon name="banknotes" class="text-green-500"/>
                                <p>Price</p>
                            </div>

                            <p>Base Price: RM <span id="base-price-{{ $ride->id }}">{{ $ride->price }}</span></p>
                            <p>Surge Price: RM <span id="surge-price-{{ $ride->id }}">0.00</span></p>
                            <p>Total Price: RM <span id="total-price-{{ $ride->id }}">{{ $ride->price }}</span></p>
                        </div>
                    </x-bladewind::card>

                </div>

                <div class="grid col-span-3 gap-3">
                    <p>Driver / Rider information</p>
                </div>
            </div>
        </div>
    @endsection

    @section('custom-js')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
        <script>
            function initMap() {
                var directionsService = new google.maps.DirectionsService();
                var directionsRenderer = new google.maps.DirectionsRenderer();
                var map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 7,
                    center: { lat: 0, lng: 0 }, // Temporary center
                    streetViewControl: false, // Disable the yellow Pegman icon
                    mapTypeControl: false, // Disable the map/satellite/etc. toggle
                });
                directionsRenderer.setMap(map);

                // Fetch departure and destination from Laravel
                var departurePlaceId = "{{ $ride->departure_id }}";
                var destinationPlaceId = "{{ $ride->destination_id }}";

                var request = {
                    origin: { placeId: departurePlaceId },
                    destination: { placeId: destinationPlaceId },
                    travelMode: 'DRIVING' // Change to WALKING, BICYCLING, or TRANSIT if needed
                };

                directionsService.route(request, function(result, status) {
                    if (status === 'OK') {
                        directionsRenderer.setDirections(result);
                    } else {
                        mapElement.innerHTML = "<p style='text-align:center; font-size:18px; color:red;'>Could not display route for this ride.</p>";
                    }
                });
            }

            // Initialize the map after the page loads
            window.onload = initMap;
        </script>
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
                                icon: "Error",
                                title: "Failed to fetch price"
                            });
                        }
                    });
                }

                //Update prices on page load
                $(document).ready(function() {
                    updatePrice({{ $ride->id }});

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
