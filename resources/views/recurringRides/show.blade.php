<x-layout>
    @section('custom-css')
    @endsection

    @section('title', 'Ride Details')

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
                        <p>Start Date: {{ \Carbon\Carbon::parse($recurringRide->start_date)->format('M j, Y') }}</p>
                        <p>End Date: {{ \Carbon\Carbon::parse($recurringRide->end_date)->format('M j, Y') }}</p>
                    </div>
                </div>
                <div class="flex gap-4 bg-white border-2 border-gray-200 rounded-md p-4 text-blue-950 text-2xl font-serif tracking-wider shadow-sm items-center">
                    <div>
                        <x-bladewind::icon name="clock" />
                    </div>
                    <div class="font-bold">
                        {{ \Carbon\Carbon::parse($firstRide->departure_time)->format('h:i A') }}
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
                                content="{{ $firstRide->departure_address }}"
                            />
                            <x-bladewind::timeline
                                date="Destination"
                                icon="flag"
                                content="{{ $firstRide->destination_address }}"
                            />
                        </x-bladewind::timeline-group>
                    </x-bladewind::card>

                    <div class="flex gap-5">
                        <x-bladewind::card compact="true" class="flex-auto">
                            <div class="mx-2">
                                <div class="text-5xl text-center font-bold text-blue-950 py-6">
                                    {{ $firstRide->number_of_passenger }}
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
                                        {{ $firstRide->distance }}
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
                                        {{ $firstRide->duration }}
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
                            <div class="flex gap-2 mb-3">
                                <p class="font-bold text-lg text-gray-600">Price Breakdown</p>
                                <x-bladewind::icon name="banknotes" class="text-green-500"/>
                            </div>

                            <div class="flex flex-auto gap-3 items-center">
                                <div>
                                    <p class="font-semibold">Base Price</p> RM <span id="base-price-{{ $firstRide->id }}">{{ $firstRide->price }}</span>
                                </div>
                                <div>+</div>
                                <div>
                                    <p class="font-semibold">Surge Price</p> RM <span id="surge-price-{{ $firstRide->id }}">0.00</span>
                                </div>
                                <div>=</div>
                                <div>
                                    <p class="font-semibold">Total Price</p> RM <span id="total-price-{{ $firstRide->id }}">{{ $firstRide->price }}</span>
                                </div>
                            </div>
                        </div>
                    </x-bladewind::card>

                    <x-bladewind::card compact="true">
                        <div class="ml-2">
                            <div class="font-bold text-lg mb-2 text-gray-600">
                                Included Rides
                            </div>
                            <div class="flex flex-auto gap-4">
                                @foreach($recurringRide->rides as $index => $ride)
                                    <div class="px-5 pt-4 shadow-sm border-2 rounded-md">
                                        <p class="font-semibold">
                                            Ride #{{ $loop->iteration }}
                                        </p>
                                        <p>
                                            <x-bladewind::icon name="calendar" class="text-blue-800" /> {{ \Carbon\Carbon::parse($ride->departure_date)->format('M j, Y') }}
                                        </p>
                                        <br>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </x-bladewind::card>

                    <x-bladewind::card compact="true">
                        <div class="ml-2">
                            <div class="font-bold text-lg mb-2 text-gray-600">
                                Description
                            </div>
                            <div>
                                @if($firstRide->description)
                                    {!! nl2br(e($firstRide->description)) !!}
                                @else
                                    No description
                                @endif
                            </div>

                        </div>
                    </x-bladewind::card>

                </div>

                <div class="grid col-span-3 gap-3">
                    <div>
                        <div>
                            <p>Driver / Rider information</p>
                        </div>

                        @auth
                            <form id="booking-form" action="/bookings" method="POST" class="flex flex-row">
                                @csrf

                                <input type="number" name="recurring_id" class="hidden" value="{{ $firstRide->recurring_id }}">
                                <input type="number" name="receiver_id" class="hidden" value="{{ $firstRide->user_id }}">

                                <button id="btnBook" class="grid bg-green-500 text-white rounded-xl w-full justify-items-center font-bold py-2 hover:bg-green-600 hover:shadow-md hover:shadow-gray-400">
                                    Book Now
                                </button>
                            </form>
                        @endauth

                    </div>

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
                var departurePlaceId = "{{ $firstRide->departure_id }}";
                var destinationPlaceId = "{{ $firstRide->destination_id }}";

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
            //handle booking
            $(document).ready(function(){
                $('#booking-form').on('submit', function(event){
                    event.preventDefault();

                    Swal.fire({
                        title: "Confirm your recurring ride booking?",
                        text: "This will send a booking request.",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonColor: "#2ac315",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Book Now"
                    }).then((result) => {
                        if (result.isConfirmed) {

                            Swal.fire({
                                title: 'Loading...',
                                text: 'Please wait while we process your request.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            $.ajax({
                                url: $(this).attr('action'),
                                method: $(this).attr('method'),
                                data: $(this).serialize(),
                                dataType: "json",
                                success: function(response) {
                                    Swal.close();
                                    if(response.success) {
                                        const Toast = Swal.mixin({
                                            toast: true,
                                            position: "top",
                                            showConfirmButton: false,
                                            timer: 2000,
                                            timerProgressBar: true,
                                            didOpen: (toast) => {
                                                toast.onmouseenter = Swal.stopTimer;
                                                toast.onmouseleave = Swal.resumeTimer;
                                            }
                                        });
                                        Toast.fire({
                                            icon: "success",
                                            title: "Recurring ride booked successfully. Redirecting to Rides page..."
                                        });
                                        setTimeout(function () {
                                            window.location.replace('/rides');
                                        }, 2000);
                                    }else{
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Failed to book recurring ride. Please try again.',
                                            text: response.message
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    Swal.close();

                                    if(xhr.status === 0){
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Failed to create ride.</br>Please try again.',
                                            text: `Error ${xhr.status}: No internet connection or Server is down.`
                                        });
                                    }else{
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Failed to book ride.</br>Please try again.',
                                            text: `Error ${xhr.status}: ${xhr.statusText}`
                                        });
                                    }
                                }
                            });
                        }
                    });
                });
            });

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
                updatePrice({{ $firstRide->id }});

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

