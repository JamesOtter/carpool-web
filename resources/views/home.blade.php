<x-layout>
    @section('custom-css')
    @endsection

    @section('title', 'Home Page')

    @section('heading')
    @endsection

    @section('content')
        <div>
            <div>
                <img src="{{ asset('images/home-part1.jpg') }}" alt="Homepage Part 1" class="w-full">
                <img src="{{ asset('images/home-part2.jpg') }}" alt="Homepage Part 2" class="w-full">
                <img src="{{ asset('images/home-part3.jpg') }}" alt="Homepage Part 3" class="w-full">
                <img src="{{ asset('images/home-part4.jpg') }}" alt="Homepage Part 4" class="w-full">
                <img src="{{ asset('images/home-part5.jpg') }}" alt="Homepage Part 5" class="w-full">
                <img src="{{ asset('images/home-part6.jpg') }}" alt="Homepage Part 5" class="w-full">
                <img src="{{ asset('images/home-part7.jpg') }}" alt="Homepage Part 5" class="w-full">
            </div>
        </div>
    @endsection

    @section('custom-js')
    @endsection

</x-layout>
