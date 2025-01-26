@props(['name', 'placeholder' => '', 'label' => '', 'id', 'required' => false])
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
    />
</div>

<script>
    function initAutocomplete() {
        const inputs = document.querySelectorAll('.autocomplete-input');
        inputs.forEach(input => {
            const autocomplete = new google.maps.places.Autocomplete(input);

            // Add event listener to handle place selection
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    // User entered the name of a Place that was not suggested and pressed the Enter key
                    input.value = '';
                }
            });

            // Prevent manual input
            input.addEventListener('blur', function() {
                const place = autocomplete.getPlace();
                if (!place || !place.geometry) {
                    input.value = '';
                }
            });
        });
    }
</script>
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initAutocomplete"
    async
    defer
></script>
