@props(['name',
        'placeholder' => null,
        'label' => null,
        'id',
        'required' => false,
        'error_message' => null,
        'need_id' => false,
        'place_id' => null,
        'value' => null,
        'old_place_id_value' => null,
        'ride_id_value' => 'null'
        ])
<div>
    <x-bladewind::input
        :name="$name"
        :placeholder="$placeholder"
        :label="$label"
        prefix="map-pin"
        prefix_is_icon="true"
        id="{{ $id }}"
        class="autocomplete-input"
        required="{{ $required }}"
        error_message="{{ $error_message }}"
        data-place-id="{{ $place_id }}"
        value="{{ $value }}"
    />

    @if($need_id)
        <input type="hidden" name="{{ $place_id }}" id="{{ $place_id }}" value="{{ $old_place_id_value }}">
    @endif
</div>

<script>
    function calculateDistanceAndDuration() {
        const departureAddress = document.getElementById('departure_address').value ?? document.getElementById('departure_address_{{ $ride_id_value }}').value;
        const destinationAddress = document.getElementById('destination_address').value ?? document.getElementById('destination_address_{{ $ride_id_value }}').value;

        if (!departureAddress || !destinationAddress) {
            return;
        }

        // Initialize the Directions service and Distance Matrix service
        const service = new google.maps.DistanceMatrixService();

        service.getDistanceMatrix(
            {
                origins: [departureAddress],
                destinations: [destinationAddress],
                travelMode: 'DRIVING',  // You can change this to WALKING, BICYCLING, etc.
            },
            function(response, status) {
                if (status === 'OK') {
                    const results = response.rows[0].elements[0];
                    const distanceInMeters = results.distance.value;  // Distance in meters
                    const durationInSeconds = results.duration.value;  // Duration in seconds

                    const distanceInKilometers = distanceInMeters / 1000;
                    const durationInMinutes = Math.round(durationInSeconds / 60);

                    if({{ $ride_id_value }} !== 'null'){
                        document.getElementById('distance_{{ $ride_id_value }}').value = distanceInKilometers.toFixed(2);  // Show distance with 2 decimal places
                        document.getElementById('duration_{{ $ride_id_value }}').value = durationInMinutes;
                    }else{
                        document.getElementById('distance').value = distanceInKilometers.toFixed(2);  // Show distance with 2 decimal places
                        document.getElementById('duration').value = durationInMinutes;
                    }

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to calculate distance and duration.</br>Please refresh the page.',
                        text: 'Distance Matrix request failed due to:', status
                    });
                }
            }
        );
    }

    function initAutocomplete() {
        const inputs = document.querySelectorAll('.autocomplete-input');

        inputs.forEach(input => {
            const autocomplete = new google.maps.places.Autocomplete(input);
            let needId = input.hasAttribute('data-place-id');
            let placeIdSelector = input.getAttribute('data-place-id');

            const placeIdInput = document.querySelector(`input[name="${placeIdSelector}"]`);

            // Add event listener to handle place selection
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    // User entered the name of a Place that was not suggested and pressed the Enter key
                    input.value = '';

                    if(needId && placeIdInput) {
                        placeIdInput.value = '';
                    }
                }else{
                    if(needId && placeIdInput) {
                        placeIdInput.value = place.place_id;
                    }
                }

                if ((document.getElementById('departure_address').value && document.getElementById('destination_address').value) ||
                    (document.getElementById('departure_address_{{ $ride_id_value }}').value && document.getElementById('destination_address_{{ $ride_id_value }}').value)) {
                    calculateDistanceAndDuration();  // Call the function when both addresses are entered
                }
            });

            // Prevent manual input
            input.addEventListener('blur', function() {
                const place = autocomplete.getPlace();
                if (!place || !place.geometry) {
                    input.value = '';
                    if (needId && placeIdInput) {
                        placeIdInput.value = '';
                    }
                }
            });
        });
    }
</script>

