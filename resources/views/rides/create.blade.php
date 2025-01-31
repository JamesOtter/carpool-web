<x-layout>

    @section('custom-css')
    @endsection

    @section('title', 'Create Ride')

    @section('heading')
        <div class="pb-3 md:px-32">
            <x-breadcrumbs :breadcrumbs="[
            ['name' => 'Rides', 'link' => '/rides'],
            ['name' => 'Create', 'link' => null],
        ]"/>
            <div>
                Create Ride Form
            </div>
        </div>
    @endsection

    @section('content')
        <form method="POST" action="/rides" id="create-ride-form">
            @csrf

            <div class="md:px-32">
                <div>
                    <div class="flex flex-auto gap-4">
                        <div class="grow">
                            <label for="">Departure</label>
                            <x-location-input
                                name="departure_address"
                                placeholder="Enter departure address"
                                id="departure"
                                required="true"
                                error_message="You will need to enter departure address"
                            />
                        </div>

                        <div>
                            <input type="hidden" id="departure_id" name="departure_id">
                        </div>

                        <div class="place-self-center">
                            <x-bladewind::icon name="arrow-right-circle" class="text-green-500 h-10 w-10"/>
                        </div>
                        <div class="grow">
                            <label for="">Destination</label>
                            <x-location-input
                                name="destination_address"
                                placeholder="Enter destination address"
                                id="destination"
                                required="true"
                                error_message="You will need to enter destination address"
                            />
                        </div>
                    </div>

                    <div>
                        <input type="hidden" id="destination_id" name="destination_id">
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <div class="grow">
                            <label for="">Select a date</label>
                            <x-bladewind::datepicker
                                min_date="{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}"
                                placeholder="Select a date"
                                required="true"
                                name="departure_date"
                                error_message="You will need to select a date"
                            />
                        </div>
                        <div class="grid-rows-2">
                            <div class="grow">
                                <label for="">Select a time</label>
                            </div>
                            <div>
                                <x-bladewind::timepicker
                                    format="24"
                                    required="true"
                                    name="departure_time"
                                />
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
                                error_message="You will need to select a ride type"
                            />
                        </div>
                        <div class="grow">
                            <label for="">Number of passenger</label>
                            <x-bladewind::input
                                name="number_of_passenger"
                                numeric="true"
                                placeholder="No. of Passenger"
                                prefix="users"
                                prefix_is_icon="true"
                                required="true"
                                error_message="You will need to enter number of passenger"
                            />
                        </div>

                        <div>
                            <input type="hidden" name="distance">
                        </div>

                        <div>
                            <input type="hidden" name="duration">
                        </div>

                        <div class="grow">
                            <label for="">Price</label>
                            <x-bladewind::input
                                name="price"
                                placeholder="0.00"
                                prefix="RM"
                                transparent_prefix="false"
                                required="true"
                                numeric="true"
                                error_message="You will need to enter price"
                            />
                        </div>
                    </div>
                    <div>
                        <label for="">Description</label>
                        <x-bladewind::textarea
                            name="description"
                            placeholder="Add more description about your ride"
                            rows="10"
                        />
                    </div>
                </div>

                <div class="place-self-end">
                    <x-bladewind::button
                        name="btn-save"
                        radius="medium"
                        has_spinner="true"
                        can_submit="true"
                    >
                        Post Now
                    </x-bladewind::button>
                </div>
            </div>
        </form>
    @endsection

    @section('custom-js')
    @endsection
</x-layout>




