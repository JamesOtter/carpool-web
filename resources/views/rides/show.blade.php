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
            <div id="map" style="width: 100%; height: 500px;"></div>
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
                    center: { lat: 0, lng: 0 } // Temporary center
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
    @endsection

</x-layout>
