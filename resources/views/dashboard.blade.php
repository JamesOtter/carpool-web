<x-layout>
    @section('custom-css')
    @endsection

    @section('title', 'Dashboard')

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
                    <x-bladewind::tab-heading name="incoming-booking"
                                              label="Incoming Booking"
                                              icon="arrow-right-end-on-rectangle" />
                    <x-bladewind::tab-heading name="outgoing-booking"
                                              label="Outgoing Booking"
                                              icon="arrow-left-start-on-rectangle" />
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
                                    <th>Updated at</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </x-slot>
                                @foreach($rides as $ride)
                                    <tr>
                                        <td>{{ $ride->id }}</td>
                                        <td>
                                            @if($ride->ride_type === 'request')
                                                <x-bladewind::tag label="Ride {{ $ride->ride_type }}" color="orange" rounded="true" class="font-semibold" />
                                            @else
                                                <x-bladewind::tag label="Ride {{ $ride->ride_type }}" color="cyan" rounded="true" class="font-semibold" />
                                            @endif
                                        </td>
                                        <td>{{ $ride->departure_address }}</td>
                                        <td>{{ $ride->destination_address }}</td>
                                        <td>{{ $ride->departure_date }}</td>
                                        <td>{{ $ride->departure_time }}</td>
                                        <td>{{ $ride->number_of_passenger }}</td>
                                        <td>{{ $ride->price }}</td>
                                        <td>{{ $ride->updated_at->diffForHumans() }}</td>
                                        <td>
                                            @if($ride->status === 'active')
                                                <x-bladewind::tag label="Active" color="green" rounded="true" class="font-semibold" />
                                            @else
                                                <x-bladewind::tag label="Inactive" color="red" rounded="true" class="font-semibold" />
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex">
                                                <div>
                                                    <button
                                                       class="edit-button px-3 py-1 mr-2 rounded-md bg-blue-500 text-white hover:bg-blue-600"
                                                       onclick="showModal('edit-ride-{{ $ride->id }}')"
                                                    >
                                                        Edit
                                                    </button>
                                                    <x-bladewind::modal
                                                        name="edit-ride-{{ $ride->id }}"
                                                        blur_size="small"
                                                        size="xl"
                                                        ok_button_label=""
                                                        cancel_button_label=""
                                                        show_close_icon="true"
                                                        title="Editing Ride - {{ $ride->id }}"
                                                        close_after_action="false"
                                                    >
                                                        <form action="/rides/{{ $ride->id }}" method="POST" id="edit-ride-form-{{ $ride->id }}">
                                                            @csrf
                                                            @method('patch')

                                                            <div class="flex gap-4">
                                                                <div class="grow">
                                                                    <label for="">Departure</label>
                                                                    <x-location-input
                                                                        name="departure_address-{{ $ride->id }}"
                                                                        placeholder="Enter departure address"
                                                                        id="departure_address_{{ $ride->id }}"
                                                                        required="true"
                                                                        :need_id="true"
                                                                        place_id="departure_id_{{ $ride->id }}"
                                                                        old_place_id_value="{{ $ride->departure_id }}"
                                                                        ride_id_value="{{ $ride->id }}"
                                                                        value="{!! $ride->departure_address !!}"
                                                                    />
                                                                </div>

                                                                <div class="place-self-center">
                                                                    <x-bladewind::icon name="arrow-right-circle" class="text-green-500 h-10 w-10"/>
                                                                </div>

                                                                <div class="grow">
                                                                    <label for="">Destination</label>
                                                                    <x-location-input
                                                                        name="destination_address-{{ $ride->id }}"
                                                                        placeholder="Enter destination address"
                                                                        id="destination_address_{{ $ride->id }}"
                                                                        required="true"
                                                                        :need_id="true"
                                                                        place_id="destination_id_{{ $ride->id }}"
                                                                        old_place_id_value="{{ $ride->destination_id }}"
                                                                        ride_id_value="{{ $ride->id }}"
                                                                        value="{!! $ride->destination_address !!}"
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
                                                                        name="departure_date-{{ $ride->id }}"
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
                                                                            name="departure_time-{{ $ride->id }}"
                                                                            selected_value="{{ $ride->departure_time }}"
                                                                        />
                                                                    </div>
                                                                </div>
                                                                <div class="hidden">
                                                                    @php
                                                                        $ride_type = [
                                                                            [ 'label' => 'Request', 'value' => 'request' ],
                                                                            [ 'label' => 'Offer', 'value' => 'offer' ],
                                                                        ];
                                                                    @endphp
                                                                    <label for="">Ride type</label>
                                                                    <x-bladewind::select
                                                                        name="ride_type-{{ $ride->id }}"
                                                                        placeholder="Ride type"
                                                                        :data="$ride_type"
                                                                        required="true"
                                                                        selected_value="{{ $ride->ride_type }}"
                                                                    />
                                                                </div>
                                                                <div class="grow">
                                                                    <label for="">Number of passenger</label>
                                                                    <x-bladewind::input
                                                                        name="number_of_passenger-{{ $ride->id }}"
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
                                                                        name="price-{{ $ride->id }}"
                                                                        placeholder="0.00"
                                                                        prefix="RM"
                                                                        transparent_prefix="false"
                                                                        required="true"
                                                                        numeric="true"
                                                                        value="{{ $ride->price }}"
                                                                    />
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <input type="hidden" name="distance_{{ $ride->id }}" id="distance_{{ $ride->id }}" value="{{ $ride->distance }}">
                                                            </div>

                                                            <div>
                                                                <input type="hidden" name="duration_{{ $ride->id }}" id="duration_{{ $ride->id }}" value="{{ $ride->duration }}">
                                                            </div>

                                                            <div class="flex flex-wrap gap-4">
                                                                <div class="grow hidden" id="vehicle_plate_number_field_{{ $ride->id }}">
                                                                    <label for="">Vehicle number</label>
                                                                    <x-bladewind::input
                                                                        name="vehicle_number-{{ $ride->id }}"
                                                                        placeholder="Enter vehicle plate number"
                                                                        required="true"
                                                                        value="{{ $ride->offer->vehicle_number ?? null}}"
                                                                    />
                                                                </div>
                                                                <div class="grow hidden" id="vehicle_model_field_{{ $ride->id }}">
                                                                    <label for="">Vehicle Model</label>
                                                                    <x-bladewind::input
                                                                        name="vehicle_model-{{ $ride->id }}"
                                                                        placeholder="Enter vehicle model"
                                                                        required="true"
                                                                        value="{{ $ride->offer->vehicle_model ?? null}}"
                                                                    />
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <label for="">Description</label>
                                                                <x-bladewind::textarea
                                                                    name="description-{{ $ride->id }}"
                                                                    placeholder="Add more description about your ride"
                                                                    rows="10"
                                                                    selected_value="{{ $ride->description }}"
                                                                />
                                                            </div>
                                                            <div class="grid justify-items-end">
                                                                <x-bladewind::button
                                                                    name="btn-save-{{ $ride->id }}"
                                                                    radius="medium"
                                                                    has_spinner="true"
                                                                    class="shadow-md shadow-blue-200 hover:shadow-blue-400"
                                                                    data-ride-id="{{ $ride->id }}"
                                                                >
                                                                    Save Changes
                                                                </x-bladewind::button>
                                                            </div>
                                                        </form>
                                                    </x-bladewind::modal>
                                                </div>
                                                <div>
                                                    <button
                                                        class="delete-button px-3 py-1 rounded-md bg-red-500 text-white hover:bg-red-600"
                                                        data-ride-id="{{ $ride->id }}"
                                                    >
                                                        Delete
                                                    </button>
                                                    <form id="delete-ride-form-{{ $ride->id }}" method="POST" action="/rides/{{ $ride->id }}" class="hidden">
                                                        @csrf
                                                        @method('delete')
                                                    </form>
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
                            >
                                <button
                                    class="m-2 px-3 py-1 bg-blue-500 text-white font-semibold rounded-md"
                                    onclick="window.location.href='/rides/create'"
                                >
                                    Post Ride
                                </button>
                            </x-bladewind::empty-state>
                        @endif
                    </x-bladewind::tab-content>

                    <x-bladewind::tab-content name="incoming-booking">
                        @if($bookings->count())
                            <x-bladewind::table>
                                <x-slot name="header">
                                    <th>Id</th>
                                    <th>Ride Id</th>
                                    <th>Sender Name</th>
                                    <th>Receiver Name</th>
                                    <th>Status</th>
                                    <th>Updated at</th>
                                    <th>Actions</th>
                                </x-slot>

                                @foreach($bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->id }}</td>
                                        <td>{{ $booking->ride_id }}</td>
                                        <td>{{ $booking->sender->name }}</td>
                                        <td>{{ $booking->receiver->name }}</td>
                                        <td>{{ $booking->status }}</td>
                                        <td>{{ $booking->updated_at->diffForHumans() }}</td>
                                        <td>
                                            <div class="flex gap-3">
                                                <button class="px-2 py-1 bg-green-500 rounded-md text-white hover:bg-green-600 hover:shadow-md">
                                                    Accept
                                                </button>
                                                <button class="px-2 py-1 bg-red-500 rounded-md text-white hover:bg-red-600 hover:shadow-md">
                                                    Decline
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </x-bladewind::table>

                        @else
                            <x-bladewind::empty-state
                                message="There are no bookings available"
                                heading="Book Ride Now"
                            >
                                <button
                                    class="m-2 px-3 py-1 bg-blue-500 text-white font-semibold rounded-md"
                                    onclick="window.location.href='/rides'"
                                >
                                    Browse Ride
                                </button>
                            </x-bladewind::empty-state>
                        @endif
                    </x-bladewind::tab-content>

                    <x-bladewind::tab-content name="outgoing-booking">
                        @if($bookings->count())
                            <x-bladewind::table>
                                <x-slot name="header">
                                    <th>Id</th>
                                    <th>Ride Id</th>
                                    <th>Sender Name</th>
                                    <th>Receiver Name</th>
                                    <th>Status</th>
                                    <th>Updated at</th>
                                    <th>Actions</th>
                                </x-slot>

                                @foreach($bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->id }}</td>
                                        <td>{{ $booking->ride_id }}</td>
                                        <td>{{ $booking->sender->name }}</td>
                                        <td>{{ $booking->receiver->name }}</td>
                                        <td>{{ $booking->status }}</td>
                                        <td>{{ $booking->updated_at->diffForHumans() }}</td>
                                        <td>
                                            <div class="flex gap-3">
                                                <button class="px-2 py-1 bg-green-500 rounded-md text-white hover:bg-green-600 hover:shadow-md">
                                                    Accept
                                                </button>
                                                <button class="px-2 py-1 bg-red-500 rounded-md text-white hover:bg-red-600 hover:shadow-md">
                                                    Decline
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </x-bladewind::table>

                        @else
                            <x-bladewind::empty-state
                                message="There are no bookings available"
                                heading="Book Ride Now"
                            >
                                <button
                                    class="m-2 px-3 py-1 bg-blue-500 text-white font-semibold rounded-md"
                                    onclick="window.location.href='/rides'"
                                >
                                    Browse Ride
                                </button>
                            </x-bladewind::empty-state>
                        @endif
                    </x-bladewind::tab-content>

                </x-bladewind::tab-body>
            </x-bladewind::tab-group>
        </div>
    @endsection

    @section('custom-js')
        <script>
            // Handle ride type dropdowns
            document.addEventListener('DOMContentLoaded', function() {
                // Select all ride type dropdowns
                document.querySelectorAll('[class^="bw-ride_type_"]').forEach(selectElement => {
                    const rideId = selectElement.classList[0].split('_').pop(); // Extract ride ID from class
                    const vehiclePlateField = document.getElementById(`vehicle_plate_number_field_${rideId}`);
                    const vehicleModelField = document.getElementById(`vehicle_model_field_${rideId}`);

                    if (!vehiclePlateField || !vehicleModelField) return; // Skip if elements are not found

                    // Function to toggle fields based on the ride type
                    function toggleVehicleFields() {
                        if (selectElement.value === 'offer') {
                            vehiclePlateField.classList.remove('hidden');
                            vehicleModelField.classList.remove('hidden');
                        } else {
                            vehiclePlateField.classList.add('hidden');
                            vehicleModelField.classList.add('hidden');
                        }
                    }

                    // Initialize visibility on page load
                    toggleVehicleFields();

                    // Add event listener to detect change in ride type
                    selectElement.addEventListener('change', toggleVehicleFields);
                });
            });
        </script>
        <script>
            // Handle edit ride form original values
            $(document).ready(function () {
                $(".edit-button").click(function () {

                    let rideId = $(this).attr("onclick").match(/edit-ride-(\d+)/)[1];
                    let form = $(`#edit-ride-form-${rideId}`);

                    if (!form.data("original")) {
                        form.data("original", form.serializeArray()); // Store original values
                    } else {
                        let originalData = form.data("original");
                        $.each(originalData, function (i, field) {
                            form.find(`[name="${field.name}"]`).val(field.value);
                        });
                    }
                });

                $(".modal-body").click(function (event) {
                    if ($(event.target).hasClass("modal-body") || $(event.target).closest(".bw-close").length) {
                        let form = $(this).find("form");
                        if (form.data("original")) {
                            let originalData = form.data("original");
                            $.each(originalData, function (i, field) {
                                form.find(`[name="${field.name}"]`).val(field.value);
                            });
                        }
                    }
                });
            });
        </script>
        <script>
            // Handle ajax request to edit ride
            $(document).ready(function() {
                $('.bw-button').on('click', function(event) {
                    event.preventDefault();

                    let rideId = $(this).data('ride-id');
                    let form = $("#edit-ride-form-" + rideId);

                    Swal.fire({
                        title: 'Loading...',
                        text: 'Please wait while we process your request.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: form.attr('action'),
                        method: form.attr('method'),
                        data: form.serialize(),
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
                                    title: "Ride edited successfully. Redirecting to Dashboard..."
                                });
                                setTimeout(function () {
                                    window.location.replace('/dashboard');
                                }, 2000);
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to edit ride. Please try again.',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();

                            if(xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;

                                // Clear previous error messages
                                $('.text-red-500').remove();
                                $('input').removeClass('border-red-400');

                                for (let key in errors) {
                                    // Find the input field dynamically by using the key as a prefix
                                    let input = $('[name^=' + key + '_]'); // Select input fields with the correct prefix
                                    if (input.length === 0) {
                                        input = $('[name=' + key + ']'); // Fallback to exact match (in case numbering isn't used)
                                    }
                                    input.addClass('border-red-400');
                                    input.after('<p class="text-red-500 text-sm mb-2">' + errors[key][0] + '</p>');
                                }
                            }else if(xhr.status === 0){
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to edit ride.</br>Please try again.',
                                    text: `Error ${xhr.status}: No internet connection or Server is down.`
                                });
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to edit ride.</br>Please try again.',
                                    text: `Error ${xhr.status}: ${xhr.statusText}`
                                });
                            }
                        }
                    });
                });
            });
        </script>
        <script>
            // Handle delete ride button
            $(document).ready(function() {
                $('.delete-button').on('click', function (event) {
                    event.preventDefault();

                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {

                            let rideId = $(this).data('ride-id');
                            let form = $("#delete-ride-form-" + rideId);

                            $.ajax({
                                url: form.attr('action'),
                                method: form.attr('method'),
                                data: form.serialize(),
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
                                            title: "Ride deleted successfully. Redirecting to Dashboard..."
                                        });
                                        setTimeout(function () {
                                            window.location.replace('/dashboard');
                                        }, 2000);
                                    }else{
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Failed to delete ride. Please try again.',
                                            text: response.message
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    Swal.close();

                                    if(xhr.status === 0){
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Failed to delete ride.</br>Please try again.',
                                            text: `Error ${xhr.status}: No internet connection or Server is down.`
                                        });
                                    }else{
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Failed to delete ride.</br>Please try again.',
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
    @endsection
</x-layout>


