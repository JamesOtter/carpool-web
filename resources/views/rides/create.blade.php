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

    <div class="md:px-32">
        <div>
            <div>
                <label for="">Departure</label>
                <x-bladewind::input
                    name="departure"
                    placeholder="Departure"
                    prefix="map-pin"
                    prefix_is_icon="true"
                />
            </div>
            <div>
                <label for="">Destination</label>
                <x-bladewind::input
                    name="destination"
                    placeholder="Destination"
                    prefix="map-pin"
                    prefix_is_icon="true"
                />
            </div>
            <div>
                <label for="">Select a date</label>
                <x-bladewind::datepicker
                    min_date="{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}"
                    placeholder="Select a date"
                />
            </div>
            <div>
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
                />
            </div>
            <div>
                <label for="">Number of passenger</label>
                <x-bladewind::input
                    numeric="true"
                    placeholder="No. of Passenger"
                />
            </div>
        </div>
    </div>

</x-layout>
