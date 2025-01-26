<x-layout>
    <x:slot:header>

    </x:slot:header>

    <x:slot:title>
        Create Ride
    </x:slot:title>

    <x:slot:heading>
        <div class="pb-3 md:px-32">
            <x-breadcrumbs :breadcrumbs="[
                ['name' => 'Rides', 'link' => '/rides'],
                ['name' => 'Create', 'link' => null],
            ]"/>
            <div>
                Create Ride Form
            </div>
        </div>
    </x:slot:heading>

    <form method="POST" action="/rides">
        @csrf

        <div class="md:px-32">
            <div>
                <div class="flex flex-auto gap-4">
                    <div class="grow">
                        <label for="">Departure</label>
                        <x-location-input
                            name="departure"
                            placeholder="Enter departure address"
                            id="departure"
                            required="true"
                        />
                    </div>
                    <div class="place-self-center">
                        <x-bladewind::icon name="arrow-right-circle" class="text-green-500 h-10 w-10"/>
                    </div>
                    <div class="grow">
                        <label for="">Destination</label>
                        <x-location-input
                            name="destination"
                            placeholder="Enter destination address"
                            id="destination"
                            required="true"
                        />
                    </div>
                </div>

                <div class="flex flex-wrap gap-4">
                    <div class="grow">
                        <label for="">Select a date</label>
                        <x-bladewind::datepicker
                            min_date="{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}"
                            placeholder="Select a date"
                            required="true"
                        />
                    </div>
                    <div class="grid-rows-2">
                        <div class="grow">
                            <label for="">Select a time</label>
                        </div>
                        <div>
                            <x-bladewind::timepicker required="true"/>
                        </div>
                    </div>
                    <div class="grow">
                        @php
                            $ride_type = [
                                [ 'label' => 'Request', 'value' => 'request' ],
                                [ 'label' => 'Offer', 'value' => 'offer' ],
                            ];
                        @endphp
                        <label for="">Ride type</label>
                        <x-bladewind::select
                            name="ride_type"
                            placeholder="Ride type"
                            :data="$ride_type"
                            required="true"
                        />
                    </div>
                    <div class="grow">
                        <label for="">Number of passenger</label>
                        <x-bladewind::input
                            numeric="true"
                            placeholder="No. of Passenger"
                            prefix="users"
                            prefix_is_icon="true"
                            required="true"
                        />
                    </div>
                    <div class="grow">
                        <label for="">Price</label>
                        <x-bladewind::input
                            name="myr"
                            placeholder="0.00"
                            prefix="RM"
                            transparent_prefix="false"
                            required="true"
                            numeric
                        />
                    </div>
                </div>

                <div>
                    <label for="">Description</label>
                    <x-bladewind::textarea
                        placeholder="Add more description about your ride"
                        rows="10"
                    />
                </div>

                <div class="place-self-end">
                    <x-bladewind::button
                        radius="medium"
                        can_submit="true"
                    >
                        Post Now
                    </x-bladewind::button>
                </div>
            </div>
        </div>
    </form>

</x-layout>

<script>
    let autocomplete;
    function initAutocomplete() {
        const input = document.getElementById('autocomplete');
        autocomplete = new google.maps.places.Autocomplete(input);
    }
</script>
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initAutocomplete"
    async
    defer
></script>
