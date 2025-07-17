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
        <div id="step-1" class="step grid grid-cols-3 gap-4 mt-4">
            <div class="pl-32 border-r-2 border-gray-300">
                <x-bladewind::timelines
                    position="left"
                    completed="true"
                    anchor="big"
                    stacked="true"
                    color="green"
                >
                    <x-bladewind::timeline
                        date="Step 1"
                        content="Upload your timetable"
                        completed="false"/>

                    <x-bladewind::timeline
                        date="Step 2"
                        content="Fill in details"
                        completed="false" />

                    <x-bladewind::timeline
                        date="Done"
                        completed="false" />

                </x-bladewind::timelines>
            </div>

            <form
                action="/upload-timetable"
                method="POST"
                id="upload-timetable"
                enctype="multipart/form-data"
                class="col-span-2 px-12"
            >
                @csrf

                <label class="flex place-self-center m-4 text-xl text-bold font-medium" for="">
                    Step 1: Upload your timetable
                </label>
                <div>
                    <x-bladewind.filepicker
                        name="timetable"
                        placeholder_line1="Upload UTAR timetable here"
                        max_file_size="3mb"
                        accepted_file_types="image/*"
                    />
                </div>

                <x-bladewind::button
                    name="btn-save"
                    radius="medium"
                    has_spinner="true"
                    can_submit="true"
                    class="mt-4 shadow-md shadow-blue-200 hover:shadow-blue-400 flex place-self-center"
                >
                    Upload
                </x-bladewind::button>
            </form>
        </div>

        <div id="step-2" class="step grid grid-cols-3 gap-4 mt-4" style="display:none;">
            <div class="pl-32 border-r-2 border-gray-300">
                <x-bladewind::timelines
                    position="left"
                    completed="true"
                    anchor="big"
                    stacked="true"
                    color="green">

                    <x-bladewind::timeline
                        date="Step 1"
                        content="Uploaded timetable"/>

                    <x-bladewind::timeline
                        date="Step 2"
                        content="Fill in details"
                        completed="false" />

                    <x-bladewind::timeline
                        date="Done"
                        completed="false" />

                </x-bladewind::timelines>
            </div>

            <div class="px-6 col-span-2">
                <form
                    action="/timetable-rides"
                    method="POST"
                    id="create-rides-form"
                    class="grid grid-cols-2 gap-6"
                >
                    @csrf
                    <div class="border-2 p-6 my-2 rounded-lg shadow-xl bg-white">
                        <div>
                            <label for="">Home Address</label>
                            <x-location-input
                                name="home_address"
                                placeholder="Enter home address"
                                id="home_address"
                                required="true"
                                :need_id="true"
                                place_id="home_id"
                            />
                        </div>
                        <div>
                            <label for="">Start Date</label>
                            <x-bladewind::datepicker
                                min_date="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                                placeholder="Select a date"
                                required="true"
                                name="start_date"
                            />
                        </div>
                        <div>
                            <label for="">End Date</label>
                            <x-bladewind::datepicker
                                min_date="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}"
                                placeholder="Select a date"
                                required="true"
                                name="end_date"
                            />
                        </div>
                        <div>
                            <label for="">Number of passenger</label>
                            <x-bladewind::input
                                name="number_of_passenger"
                                numeric="true"
                                placeholder="No. of Passenger"
                                prefix="users"
                                prefix_is_icon="true"
                                required="true"
                                value="4"
                            />
                        </div>
                        <div>
                            <div class="grow">
                                <label for="">Base Price</label>
                                <x-bladewind::input
                                    name="price"
                                    placeholder="0.00"
                                    prefix="RM"
                                    transparent_prefix="false"
                                    required="true"
                                    numeric="true"
                                />
                            </div>
                        </div>
                        <div>
                            <div id="vehicle_plate_number_field">
                                <label for="">Vehicle number</label>
                                <x-bladewind::input
                                    name="vehicle_number"
                                    placeholder="Enter vehicle plate number"
                                    required="true"
                                />
                            </div>
                            <div id="vehicle_model_field">
                                <label for="">Vehicle Model</label>
                                <x-bladewind::input
                                    name="vehicle_model"
                                    placeholder="Enter vehicle model"
                                    required="true"
                                />
                            </div>
                        </div>
                        <div>
                            <label for="">Description</label>
                            <x-bladewind::textarea
                                name="description"
                                placeholder="Add more description about your ride"
                                rows="6"
                            />
                        </div>

                        <x-bladewind::button
                            name="btn-save"
                            radius="medium"
                            has_spinner="true"
                            can_submit="true"
                            class="mt-4 shadow-md shadow-blue-200 hover:shadow-blue-400 flex place-self-center"
                        >
                            Create Rides
                        </x-bladewind::button>
                    </div>

                    <div id="timetableResults"></div>
                </form>
            </div>
        </div>

{{--        <div id="step-3" class="step" style="display:none;">--}}
{{--            <h1>Step3</h1>--}}
{{--        </div>--}}
    @endsection

    @section('custom-js')
        <script>
            $('#upload-timetable').on('submit', function(event) {
                event.preventDefault();

                Swal.fire({
                    title: 'Loading...',
                    text: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
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
                                title: "Image received!"
                            });

                            // Go to step 2
                            $('#step-1').hide();
                            $('#step-2').show();

                            // Display extracted timetable
                            const container = document.getElementById('timetableResults');
                            response.html_blocks.forEach(html => {
                                container.insertAdjacentHTML('beforeend', html);
                            });

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

                            Swal.fire({
                                icon: 'error',
                                title: 'Failed to create ride. Please try again.',
                                text: errors['timetable'][0]
                            });
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
        </script>

        <script>
            $('#create-rides-form').on('submit', function(event) {
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
                                title: "Rides created successfully. Redirecting to Rides page..."
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
            })
        </script>
    @endsection
</x-layout>
