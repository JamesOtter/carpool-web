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
                                    <th>Updated at</th>
                                    <th>Status</th>
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
                                        <td>{{ $ride->updated_at->diffForHumans() }}</td>
                                        <td>{{ $ride->status }}</td>
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
                                                                        id="departure_address"
                                                                        required="true"
                                                                        :need_id="true"
                                                                        place_id="departure_id"
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
                                                                        id="destination_address"
                                                                        required="true"
                                                                        :need_id="true"
                                                                        place_id="destination_id"
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
                                                                <div class="grow">
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
                                                                <input type="hidden" name="distance-{{ $ride->id }}" id="distance-{{ $ride->id }}">
                                                            </div>

                                                            <div>
                                                                <input type="hidden" name="duration-{{ $ride->id }}" id="duration-{{ $ride->id }}">
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
                                                                    can_submit="true"
                                                                    class="shadow-md shadow-blue-200 hover:shadow-blue-400"
                                                                >
                                                                    Save Changes
                                                                </x-bladewind::button>
                                                            </div>
                                                        </form>
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
{{--        <script>--}}
{{--            $(document).ready(function() {--}}
{{--                $('#edit-ride-form-{{ $ride->id }}').on('submit', function(event) {--}}
{{--                    event.preventDefault();--}}

{{--                    Swal.fire({--}}
{{--                        title: 'Loading...',--}}
{{--                        text: 'Please wait while we process your request.',--}}
{{--                        allowOutsideClick: false,--}}
{{--                        didOpen: () => {--}}
{{--                            Swal.showLoading();--}}
{{--                        }--}}
{{--                    });--}}

{{--                    $.ajax({--}}
{{--                        url: $(this).attr('action'),--}}
{{--                        method: $(this).attr('method'),--}}
{{--                        data: $(this).serialize(),--}}
{{--                        dataType: "json",--}}
{{--                        success: function(response) {--}}
{{--                            Swal.close();--}}
{{--                            if(response.success) {--}}
{{--                                const Toast = Swal.mixin({--}}
{{--                                    toast: true,--}}
{{--                                    position: "top",--}}
{{--                                    showConfirmButton: false,--}}
{{--                                    timer: 2000,--}}
{{--                                    timerProgressBar: true,--}}
{{--                                    didOpen: (toast) => {--}}
{{--                                        toast.onmouseenter = Swal.stopTimer;--}}
{{--                                        toast.onmouseleave = Swal.resumeTimer;--}}
{{--                                    }--}}
{{--                                });--}}
{{--                                Toast.fire({--}}
{{--                                    icon: "success",--}}
{{--                                    title: "Ride edited successfully. Redirecting to Rides page..."--}}
{{--                                });--}}
{{--                                setTimeout(function () {--}}
{{--                                    window.location.replace('/dashboard');--}}
{{--                                }, 2000);--}}
{{--                            }else{--}}
{{--                                Swal.fire({--}}
{{--                                    icon: 'error',--}}
{{--                                    title: 'Failed to edit ride. Please try again.',--}}
{{--                                    text: response.message--}}
{{--                                });--}}
{{--                            }--}}
{{--                        },--}}
{{--                        error: function(xhr) {--}}
{{--                            Swal.close();--}}

{{--                            if(xhr.status === 422) {--}}
{{--                                let errors = xhr.responseJSON.errors;--}}

{{--                                $('.text-red-500').remove();--}}
{{--                                $('input').removeClass('border-red-400');--}}

{{--                                for (let key in errors) {--}}
{{--                                    let input = $('[name=' + key + ']');--}}
{{--                                    input.addClass('border-red-400');--}}
{{--                                    input.after('<p class="text-red-500 text-sm mb-2">' + errors[key][0] + '</p>');--}}
{{--                                }--}}
{{--                            }else if(xhr.status === 0){--}}
{{--                                Swal.fire({--}}
{{--                                    icon: 'error',--}}
{{--                                    title: 'Failed to edit ride.</br>Please try again.',--}}
{{--                                    text: `Error ${xhr.status}: No internet connection or Server is down.`--}}
{{--                                });--}}
{{--                            }else{--}}
{{--                                Swal.fire({--}}
{{--                                    icon: 'error',--}}
{{--                                    title: 'Failed to create ride.</br>Please try again.',--}}
{{--                                    text: `Error ${xhr.status}: ${xhr.statusText}`--}}
{{--                                });--}}
{{--                            }--}}
{{--                        }--}}
{{--                    });--}}
{{--                });--}}
{{--            });--}}
{{--        </script>--}}
    @endsection
</x-layout>


