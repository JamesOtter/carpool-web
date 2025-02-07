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
                                id="departure_address"
                                required="true"
                                error_message="You will need to enter departure address"
                                :need_id="true"
                                place_id="departure_id"
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
                                error_message="You will need to enter destination address"
                                :need_id="true"
                                place_id="destination_id"
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
                            <input type="hidden" name="distance" id="distance">
                        </div>

                        <div>
                            <input type="hidden" name="duration" id="duration">
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
                            />
                        </div>
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
                </div>

                <div class="place-self-end">
                    <x-bladewind::button
                        name="btn-save"
                        radius="medium"
                        has_spinner="true"
                        can_submit="true"
                        class="shadow-md shadow-blue-200 hover:shadow-blue-400"
                    >
                        Post Now
                    </x-bladewind::button>
                    <x-bladewind::button
                        name="btn-clear"
                        type="secondary"
                        radius="medium"
                        class="ml-2 mt-3 shadow-md hover:shadow-slate-500/50"
                        id="clear-all">
                        Clear All
                    </x-bladewind::button>
                </div>
            </div>
        </form>
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

        <script>
            $(document).ready(function() {
                $('#clear-all').on('click', function(event) {
                    event.preventDefault();
                    $('#create-ride-form').find('input').val('');
                });

                $('#create-ride-form').on('submit', function(event) {
                    event.preventDefault();

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
                                    title: "Ride created successfully. Redirecting to Rides page..."
                                });
                                setTimeout(function () {
                                    window.location.replace('/rides');
                                }, 2000);
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to create ride. Please try again.',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();

                            if(xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;

                                $('.text-red-500').remove();
                                $('input').removeClass('border-red-400');

                                for (let key in errors) {
                                    let input = $('[name=' + key + ']');
                                    input.addClass('border-red-400');
                                    input.after('<p class="text-red-500 text-sm mb-2">' + errors[key][0] + '</p>');
                                }
                            }else if(xhr.status === 0){
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to create ride.</br>Please try again.',
                                    text: `Error ${xhr.status}: No internet connection or Server is down.`
                                });
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to create ride.</br>Please try again.',
                                    text: `Error ${xhr.status}: ${xhr.statusText}`
                                });
                            }
                        }
                    });
                });
            });
        </script>
    @endsection
</x-layout>




