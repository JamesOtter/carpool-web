<x-layout>
    <x:slot:header>

    </x:slot:header>

    <x:slot:title>
        Register
    </x:slot:title>

    <x:slot:heading>
        <div class="pb-3 md:px-32">
            Register Account
        </div>
    </x:slot:heading>

    <div class="md:px-32">
        <form method="POST" action="/register">
            @csrf
            <div>
                <div>
                    <label for="">Name</label>
                    <x-bladewind::input
                        name="name"
                        placeholder="Name"
                        value="{{ old('name') }}"
                        required="true"
                    />
                    @error('name')
                    <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="">Email</label>
                    <x-bladewind::input
                        name="email"
                        placeholder="Email"
                        type="email"
                        value="{{ old('email') }}"
                        required="true"
                    />
                    @error('email')
                    <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="">Contact Number</label>
                    <x-bladewind::input
                        name="contact_number"
                        placeholder="Contact Number"
                        value="{{ old('contact_number') }}"
                        numeric="true"
                        required="true"
                    />
                    @error('contact_number')
                    <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="">Password</label>
                    <x-bladewind::input
                        name="password"
                        placeholder="Password"
                        type="password"
                        viewable="true"
                        required="true"
                    />
                    @error('password')
                    <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="">Confirm Password</label>
                    <x-bladewind::input
                        name="password_confirmation"
                        placeholder="Confirm password"
                        type="password"
                        viewable="true"
                        required="true"
                    />
                </div>
            </div>

            <div class="text-center">
                <x-bladewind::button
                    name="btn-save"
                    has_spinner="true"
                    type="primary"
                    can_submit="true"
                    class="mt-3">
                    Register
                </x-bladewind::button>
            </div>
        </form>
    </div>

</x-layout>


