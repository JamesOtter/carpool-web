<x-layout>
    @section('custom-css')
    @endsection

    @section('title', 'About Page')

    @section('heading')
        <div class="pb-3 md:px-32">
            <p>My Dashboard</p>
        </div>
    @endsection

    @section('content')
        <div class="md:px-32">
            <x-bladewind::tab-group name="manage-tabs" color="indigo">
                <x-slot name="headings">
                    <x-bladewind::tab-heading name="manage-rides" active="true"
                                              icon="cog-6-tooth"
                                              label="Manage Rides" />
                    <x-bladewind::tab-heading name="manage-request"
                                              label="Manage Request"
                                              icon="bell" />
                </x-slot>
                <x-bladewind::tab-body>
                    <x-bladewind::tab-content name="manage-rides" active="true">
                        @if($rides->count())
                            <x-bladewind::table>
                                <x-slot name="header">
                                    <th>ID</th>
                                    <th>Ride Type</th>
                                    <th>Departure Address</th>
                                    <th>Destination Address</th>
                                    <th>Departure Date</th>
                                    <th>Departure Time</th>
                                    <th>Number of Passenger</th>
                                    <th>Base Price (RM)</th>
                                    <th>Created at</th>
                                    <th>Actions</th>
                                </x-slot>
                                @foreach($rides as $ride)
                                    <tr>
                                        <td>{{ $ride->id }}</td>
                                        <td>{{ $ride->ride_type }}</td>
                                        <td>{{ $ride->departure_address }}</td>
                                        <td>{{ $ride->destination_address }}</td>
                                        <td>{{ $ride->departure_date }}</td>
                                        <td>{{ $ride->departure_time }}</td>
                                        <td>{{ $ride->number_of_passenger }}</td>
                                        <td>{{ $ride->price }}</td>
                                        <td>{{ $ride->created_at }}</td>
                                        <td>
                                            <div class="flex">
                                                <div>
                                                    <button
                                                       class="px-3 py-1 mr-2 rounded-md bg-blue-500 text-white hover:bg-blue-600"
                                                       onclick="showModal('edit-ride-{{ $ride->id }}')"
                                                    >
                                                        Edit
                                                    </button>
                                                    <x-bladewind::modal
                                                        name="edit-ride-{{ $ride->id }}"
                                                        blur_size="small"
                                                        size="xl"
                                                        ok_button_label="Save Changes"
                                                        ok_button_action=""
                                                        show_close_icon="true"
                                                        title="Editing Ride - {{ $ride->id }}"
                                                        close_after_action="false"
                                                    >
                                                        <form action="/rides/{{ $ride->id }}" method="POST" id="edit-ride-form-{{ $ride->id }}">
                                                            @csrf

                                                            <div class="flex gap-4">
                                                                <div class="grow">
                                                                    <label for="">Departure</label>
                                                                    <x-location-input
                                                                        name="departure_address"
                                                                        placeholder="Enter departure address"
                                                                        id="departure_address"
                                                                        required="true"
                                                                        :need_id="true"
                                                                        place_id="departure_id"
                                                                        value="{{ $ride->departure_address }}"
                                                                    />
                                                                </div>

                                                                <div class="place-self-center">
                                                                    <x-bladewind::icon name="arrow-right-circle" class="text-green-500 h-10 w-10"/>
                                                                </div>

                                                                <div class="grow">
                                                                    <label for="">Destination</label>
                                                                    <x-location-input
                                                                        name="destination_address"
                                                                        placeholder="Enter destination address"
                                                                        id="destination_address"
                                                                        required="true"
                                                                        :need_id="true"
                                                                        place_id="destination_id"
                                                                        value="{{ $ride->destination_address }}"
                                                                    />
                                                                </div>
                                                            </div>

                                                            <div class="flex gap-4">
                                                                <div class="grow">
                                                                    <label for="">Select a date</label>
                                                                    <x-bladewind::datepicker
                                                                        min_date="{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}"
                                                                        placeholder="Select a date"
                                                                        required="true"
                                                                        name="departure_date"
                                                                        default_date="{{ $ride->departure_date }}"
                                                                    />
                                                                </div>
                                                                <div>
                                                                    <div>
                                                                        <label for="">Select a time</label>
                                                                    </div>
                                                                    <div>
                                                                        <x-bladewind::timepicker
                                                                            format="24"
                                                                            required="true"
                                                                            name="departure_time"
                                                                            selected_value="{{ $ride->departure_time }}"
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
                                                                        selected_value="{{ $ride->ride_type }}"
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
                                                                        value="{{ $ride->number_of_passenger }}"
                                                                    />
                                                                </div>

                                                                <div class="grow">
                                                                    <label for="">Base Price</label>
                                                                    <x-bladewind::input
                                                                        name="price"
                                                                        placeholder="0.00"
                                                                        prefix="RM"
                                                                        transparent_prefix="false"
                                                                        required="true"
                                                                        numeric="true"
                                                                        error_message="You will need to enter price"
                                                                        value="{{ $ride->price }}"
                                                                    />
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <input type="hidden" name="distance" id="distance">
                                                            </div>

                                                            <div>
                                                                <input type="hidden" name="duration" id="duration">
                                                            </div>

                                                            <div class="flex flex-wrap gap-4">
                                                                <div class="grow hidden" id="car_plate_number_field">
                                                                    <label for="">Vehicle number</label>
                                                                    <x-bladewind::input
                                                                        name="vehicle_number"
                                                                        placeholder="Enter car plate number"
                                                                        required="true"
                                                                        error_message="You will need to enter car plate number"
                                                                    />
                                                                </div>
                                                                <div class="grow hidden" id="car_model_field">
                                                                    <label for="">Vehicle Model</label>
                                                                    <x-bladewind::input
                                                                        name="vehicle_model"
                                                                        placeholder="Enter car model"
                                                                        required="true"
                                                                        error_message="You will need to enter car model"
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
                                                        </form>

                                                        <x-bladewind::processing
                                                            name="ride-updating"
                                                            message="Updating your ride." />

                                                        <x-bladewind::process-complete
                                                            name="ride-update-yes"
                                                            process_completed_as="passed"
                                                            button_label="Done"
                                                            button_action="hideModal('edit-ride-{{ $ride->id }}')"
                                                            message="Ride updated successfully." />
                                                    </x-bladewind::modal>
                                                </div>
                                                <div>
                                                    <button
                                                        class="px-3 py-1 rounded-md bg-red-500 text-white hover:bg-red-600"
                                                    >
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </x-bladewind::table>
                        @else
                            <x-bladewind::empty-state
                                message="There are no rides available"
                                heading="Post Ride Now"
                                button_label="Post Ride"
                                onclick="window.location.href='{{ route('rides.create') }}'"
                            >
                            </x-bladewind::empty-state>
                        @endif
                    </x-bladewind::tab-content>

                    <x-bladewind::tab-content name="manage-request">
                        <p>Request list should be here</p>
                    </x-bladewind::tab-content>
                </x-bladewind::tab-body>
            </x-bladewind::tab-group>
        </div>
    @endsection

    @section('custom-js')
        <script>
            //Ride type manipulate form fields based on selection
            document.addEventListener("DOMContentLoaded", function() {
                let rideTypeSelect = document.querySelector(".bw-ride_type");
                let carPlateField = document.getElementById("car_plate_number_field");
                let carModelField = document.getElementById("car_model_field");

                function toggleFields() {
                    if (rideTypeSelect.value === "offer") {
                        carPlateField.classList.remove("hidden");
                        carModelField.classList.remove("hidden");
                    } else {
                        carPlateField.classList.add("hidden");
                        carModelField.classList.add("hidden");
                    }
                }

                toggleFields(); // Run on page load for pre-selected values

                rideTypeSelect.addEventListener("change", toggleFields);
            });
        </script>
    @endsection
</x-layout>


