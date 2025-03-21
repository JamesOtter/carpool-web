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

            <div class="grid grid-cols-10 gap-4 mt-5 mb-5">
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
                            <div class="font-bold text-lg mb-2">
                                Description
                            </div>
                            <div>
                                @if($ride->description)
                                    {!! nl2br(e($ride->description)) !!}
                                @else
                                    No description
                                @endif
                            </div>
                        </div>
                    </x-bladewind::card>

                </div>

                <div class="grid col-span-3 gap-3">
                    <div>
                        <x-bladewind::card compact="true" class="mb-4">
                            <div class="ml-2">
                                <div class="flex gap-2 mb-4">
                                    <p class="font-bold text-lg text-gray-600">Price Breakdown</p>
                                    <x-bladewind::icon name="banknotes" class="text-green-500"/>
                                </div>

                                <div>
                                    <div class="grid grid-cols-2 gap-4 mx-2">
                                        <div class="font-semibold">Base Price</div>
                                        <div>RM <span id="base-price-{{ $ride->id }}">{{ $ride->price }}</span></div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 mx-2">
                                        <div ></div>
                                        <div>+</div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 mx-2">
                                        <div class="font-semibold">Surge Price</div>
                                        <div>RM <span id="surge-price-{{ $ride->id }}">0.00</span></div>
                                    </div>

                                    <hr class="my-3 mx-2">

                                    <div class="grid grid-cols-2 gap-4 mx-2">
                                        <p class="font-bold">Total Price</p>
                                        <div class="font-bold">RM <span id="total-price-{{ $ride->id }}">{{ $ride->price }}</span></div>
                                    </div>
                                </div>
                            </div>
                        </x-bladewind::card>

                        <x-bladewind::card compact="true" class="mb-4">
                            <div>
                                <div class="text-lg font-bold text-gray-600 mb-2">
                                    Driver / Rider information
                                </div>
                                <div>
                                    <x-bladewind::contact-card
                                        name="{{ $ride->user->name }}"
                                        mobile="+{{ $ride->user->contact_number }}"
                                        image="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                                        position="{{ $ride->ride_type == 'request' ? 'Rider' : 'Driver' }}"
                                        birthday="{{ \Carbon\Carbon::parse($ride->user->created_at)->format('Y-M-d') }}" />
                                </div>
                                <div class="justify-self-center mt-5">
                                    <a href="https://wa.me/{{ $ride->user->contact_number }}?text=Hello, I'm interested in your carpool trip!" target="_blank" class="text-white">
                                        <button class="flex gap-3 border rounded-xl py-2 px-6 bg-green-500 hover:shadow-md hover:bg-green-600">
                                            <x-bladewind::icon type="solid" name="phone" class="border-2 border-white rounded-full p-1"/>
                                            <p class="font-bold">Chat on WhatsApp</p>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </x-bladewind::card>

                        @auth
                            <form id="booking-form" action="/bookings" method="POST" class="flex flex-row">
                                @csrf

                                <input type="number" name="ride_id" class="hidden" value="{{ $ride->id }}">
                                <input type="number" name="receiver_id" class="hidden" value="{{ $ride->user_id }}">

                                <button id="btnBook" class="grid bg-blue-500 text-white rounded-xl w-full justify-items-center font-bold py-2 hover:bg-blue-600 hover:shadow-md hover:shadow-gray-400">
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
            //handle booking
            $(document).ready(function(){
                $('#booking-form').on('submit', function(event){
                    event.preventDefault();

                    Swal.fire({
                        title: "Confirm your ride booking?",
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
                                            title: "Ride booked successfully. Redirecting to Rides page..."
                                        });
                                        setTimeout(function () {
                                            window.location.replace('/rides');
                                        }, 2000);
                                    }else{
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Failed to book ride. Please try again.',
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
