<x-layout>
    @section('custom-css')
    @endsection

    @section('title', 'Details Page')

    @section('heading')
            <div class="pb-3 md:px-32">
                <x-breadcrumbs :breadcrumbs="[
                    ['name' => 'Rides', 'link' => '/rides'],
                    ['name' => 'Details', 'link' => null],
                ]"/>
            </div>
    @endsection

    @section('content')
        <div class="md:px-32">
            <h1>This is details page</h1>
        </div>
    @endsection

    @section('custom-js')
    @endsection

</x-layout>
