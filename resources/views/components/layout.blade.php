<!doctype html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
{{--    For Ngrok use this--}}
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link href="{{ secure_asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
    <link href="{{ secure_asset('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />
    <script src="{{ secure_asset('vendor/bladewind/js/helpers.js') }}"></script>
    <script src="{{ secure_asset('vendor/bladewind/js/datepicker.js') }}"></script>

{{--    For local use this--}}
{{--    <script src="//unpkg.com/alpinejs" defer></script>--}}
{{--    <link href="{{ asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />--}}
{{--    <link href="{{ asset('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />--}}
{{--    <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>--}}

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    @yield('custom-css')

    <title>@yield('title')</title>

    @php
        date_default_timezone_set("Asia/Kuala_Lumpur");
    @endphp
</head>

<body class="h-full">
    <div class="flex flex-col min-h-full">
        <nav class="bg-gray-800">
            <div class="mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <a href="/">
                                <p class="text-white font-extrabold text-xl border-2 border-white rounded-xl px-3 py-1">.CarPool</p>
{{--                                <img class="size-8" src="https://tailwindui.com/plus/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company">--}}
                            </a>
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-20 flex items-baseline space-x-4">
                                <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                                <x-nav-link href="/" :active="request()->is('/')">Home</x-nav-link>
                                <x-nav-link href="/rides" :active="request()->is('rides')">Rides</x-nav-link>
                                <x-nav-link href="/dashboard" :active="request()->is('dashboard')">Dashboard</x-nav-link>
{{--                            <x-nav-link type="button" :active="request()->is('dashboard')">Dashboard</x-nav-link>--}}
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-4 flex items-center md:ml-6">
{{--                        Post ride button--}}
                            <a
                                href="/timetable-rides/create"
                                class="text-white rounded-xl mx-3 px-3 py-1 border border-white hover:shadow-md  hover:bg-gray-700"
                            >
                                <x-bladewind::icon name="plus-circle" type="outline" />Timetable Ride
                            </a>

                            <a
                                href="/rides/create"
                                class="text-white rounded-xl mx-3 px-3 py-1 border border-white hover:shadow-md hover:bg-gray-700"
                            >
                                <x-bladewind::icon name="plus-circle" type="outline" />Post Ride
                            </a>

                            @auth
                                <button type="button" class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                                    <span class="absolute -inset-1.5"></span>
                                    <span class="sr-only">View notifications</span>
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                    </svg>
                                </button>

                                <!-- Profile dropdown -->
                                <div class="relative ml-3">
                                    <div>
                                        <button
                                            type="button"
                                            class="relative flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                                            id="user-menu-button"
                                            aria-expanded="false"
                                            aria-haspopup="true"
                                            onclick="toggleDropdown()"
                                        >
                                            <span class="absolute -inset-1.5"></span>
                                            <span class="sr-only">Open user menu</span>
                                            <img
                                                class="size-8 rounded-full"
                                                src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                                                alt=""
                                            >
                                        </button>
                                    </div>

                                    <div
                                        id="user-menu-dropdown"
                                        class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none hidden"
                                        role="menu"
                                        aria-orientation="vertical"
                                        aria-labelledby="user-menu-button"
                                        tabindex="-1"
                                    >
                                        <!-- Active: "bg-gray-100 outline-none", Not Active: "" -->
{{--                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 outline-none" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>--}}
{{--                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 outline-none" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>--}}
                                        <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-500 hover:text-white outline-none" role="menuitem" tabindex="-1" id="user-menu-item-2">Log out</a>
                                    </div>
                                </div>
                            @endauth
                            @guest
                                <div class="flex flex-nowrap text-white">
                                    <div>
                                        <a href="{{ route('login') }}" class="mx-2 px-4 py-2 rounded-md {{ request()->is('login') ? "bg-white text-black" : "text-white hover:bg-gray-700" }}">Log In</a>|
                                    </div>
                                    <div>
                                        <a href="{{ route('register') }}" class="mx-2 px-4 py-2 rounded-md {{ request()->is('register') ? "bg-white text-black" : "text-white hover:bg-gray-700" }}">Register</a>
                                    </div>
                                </div>
                            @endguest
                        </div>
                    </div>
                    <div class="-mr-2 flex md:hidden">
                        <!-- Mobile menu button -->
                        <button
                            id="mobile-menu-button"
                            type="button"
                            class="relative inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                            aria-controls="mobile-menu"
                            aria-expanded="false"
                        >
                            <span class="absolute -inset-0.5"></span>
                            <span class="sr-only">Open main menu</span>
                            <!-- Menu open: "hidden", Menu closed: "block" -->
                            <svg id="menu-open-icon" class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            <!-- Menu open: "block", Menu closed: "hidden" -->
                            <svg id="menu-close-icon" class="hidden size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu, show/hide based on menu state. -->
            <div class="md:hidden hidden" id="mobile-menu">
                <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
                    <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                    <x-mobile-nav-link href="/" :active="request()->is('/')">Home</x-mobile-nav-link>
                    <x-mobile-nav-link href="/rides" :active="request()->is('rides')">Rides</x-mobile-nav-link>
                    <x-mobile-nav-link href="/dashboard" :active="request()->is('dashboard')">Dashboard</x-mobile-nav-link>
                </div>
                <div class="border-t border-gray-700 pb-3 pt-4">
                    <div class="flex items-center px-5">
                        <div class="shrink-0">
                            <img class="size-10 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                        </div>
                        <div class="ml-3">
                            <div class="text-base/5 font-medium text-white">Tom Cook</div>
                            <div class="text-sm font-medium text-gray-400">tom@example.com</div>
                        </div>
                        <button type="button" class="relative ml-auto shrink-0 rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                            <span class="absolute -inset-1.5"></span>
                            <span class="sr-only">View notifications</span>
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-3 space-y-1 px-2">
                        <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Your Profile</a>
                        <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Settings</a>
                        <a href="{{ route('logout') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Log out</a>
                    </div>
                </div>
            </div>
        </nav>

        <header class="bg-white shadow">
            <div class="mx-auto px-4 pt-3 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">@yield('heading')</h1>
            </div>
        </header>

        <main class="flex-grow">
            <div class="mx-auto px-4 py-2 sm:px-6 lg:px-8">
                @yield('content')
            </div>

            @include('components.chatbot')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-6 rounded-xl m-2">
            <div class="container mx-auto px-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <!-- Copyright Notice -->
                    <div class="mb-4 md:mb-0">
                        <p class="text-sm">
                            &copy; {{ date('Y') }} CarPool. All rights reserved.
                        </p>
                    </div>

                    <!-- Footer Links -->
                    <div class="flex space-x-4">
                        <a href="#" class="text-sm hover:text-gray-400">Privacy Policy</a>
                        <a href="#" class="text-sm hover:text-gray-400">Terms of Service</a>
                        <a href="#" class="text-sm hover:text-gray-400">Contact Us</a>
                    </div>
                </div>
            </div>
        </footer>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script
        async
        defer
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=marker,places&callback=initAutocomplete"
    ></script>

    @auth
    <script>
        // Logout confirmation
        document.getElementById('user-menu-item-2').addEventListener('click', function(event) {
            event.preventDefault();

            Swal.fire({
                title: "Confirm Logout?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ea2b2b",
                cancelButtonColor: "rgba(124,124,124,0.85)",
                confirmButtonText: "Logout"
            }).then((result) => {
                if (result.isConfirmed) {
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
                        title: "Logged out"
                    });
                    setTimeout(function () {
                        window.location.replace('/logout');
                    }, 2000);
                }
            });
        });
    </script>
    <script>
        // Dropdown user menu
        function toggleDropdown() {
            const dropdown = document.getElementById('user-menu-dropdown');
            dropdown.classList.toggle('hidden');
        }

        // Optional: Close the dropdown if clicked outside
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('user-menu-dropdown');
            const button = document.getElementById('user-menu-button');
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        //mobile user menu dropdown
        document.addEventListener("DOMContentLoaded", function () {
            const menuButton = document.getElementById("mobile-menu-button");
            const mobileMenu = document.getElementById("mobile-menu");
            const menuOpenIcon = document.getElementById("menu-open-icon");
            const menuCloseIcon = document.getElementById("menu-close-icon");

            menuButton.addEventListener("click", () => {
                // Toggle the menu visibility
                const isMenuVisible = mobileMenu.classList.contains("hidden");

                if (isMenuVisible) {
                    mobileMenu.classList.remove("hidden");
                    menuOpenIcon.classList.add("hidden");
                    menuCloseIcon.classList.remove("hidden");
                } else {
                    mobileMenu.classList.add("hidden");
                    menuOpenIcon.classList.remove("hidden");
                    menuCloseIcon.classList.add("hidden");
                }
            });
        });
    </script>
    @endauth



    @yield('custom-js')
</body>

</html>
