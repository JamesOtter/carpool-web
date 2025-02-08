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
                                                        size="large"
                                                        ok_button_label="Save Changes"
                                                        ok_button_action=""
                                                        show_close_icon="true"
                                                        title="Editing Ride - {{ $ride->id }}"
                                                        close_after_action="false"
                                                    >
                                                        <form action="/rides/{{ $ride->id }}" method="POST" id="edit-ride-form-{{ $ride->id }}">
                                                            @csrf


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
    @endsection
</x-layout>


