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
                                                    <a href="#" class="px-3 py-1 mr-2 rounded-md bg-blue-500 text-white hover:bg-blue-600">Edit</a>
                                                </div>
                                                <div>
                                                    <a href="#" class="px-3 py-1 rounded-md bg-red-500 text-white hover:bg-red-600">Delete</a>
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


