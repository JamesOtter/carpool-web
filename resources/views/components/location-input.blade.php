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
        'ride_id_value' => null
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
    function calculateDistanceAndDuration(rideId) {
        let departureInput = rideId ? document.getElementById(`departure_address_${rideId}`) : document.getElementById(`departure_address`);
        let destinationInput = rideId ? document.getElementById(`destination_address_${rideId}`) : document.getElementById(`destination_address`);

        if (!departureInput || !destinationInput) return;

        let departureAddress = departureInput.value;
        let destinationAddress = destinationInput.value;

        if (!departureAddress || !destinationAddress) return;

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
                    const distanceInKilometers = (results.distance.value / 1000).toFixed(2);
                    const durationInMinutes = Math.round(results.duration.value / 60);

                    let distanceField = rideId ? document.getElementById(`distance_${rideId}`) : document.getElementById(`distance`);
                    let durationField = rideId ? document.getElementById(`duration_${rideId}`) : document.getElementById(`duration`);

                    if (distanceField) distanceField.value = distanceInKilometers;
                    if (durationField) durationField.value = durationInMinutes;

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

                let rideId = input.id.match(/_(\d+)$/) ? input.id.match(/_(\d+)$/)[1] : null;
                console.log(rideId);

                let departureAddress = rideId ? document.getElementById(`departure_address_${rideId}`)?.value : document.getElementById(`departure_address`)?.value;
                let destinationAddress = rideId ? document.getElementById(`destination_address_${rideId}`)?.value : document.getElementById(`destination_address`)?.value;

                if (departureAddress && destinationAddress) {
                    calculateDistanceAndDuration(rideId);  // Call the function when both addresses are entered
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

