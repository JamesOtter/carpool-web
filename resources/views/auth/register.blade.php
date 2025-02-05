<x-layout>

    @section('custom-css')
    @endsection

    @section('title', 'Register')

    @section('heading')
        <div class="flex pb-3 justify-center">
            Register Account
        </div>
    @endsection

    @section('content')
        <div class="w-96 place-self-center border-2 bg-white p-7 mt-2 rounded-2xl">
            <form id="register-form" method="POST" action="/register">
                @csrf
                <div>
                    <div>
                        <x-bladewind::input
                            name="name"
                            label="Name"
                            required="true"
                        />
                    </div>
                    <div>
                        <x-bladewind::input
                            name="email"
                            label="Email"
                            type="email"
                            required="true"
                        />
                    </div>
                    <div>
                        <x-bladewind::input
                            name="contact_number"
                            label="Contact Number"
                            placeholder="60XXXXXXXXXX"
                            show_placeholder_always="true"
                            numeric="true"
                            required="true"
                        />
                    </div>
                    <div>
                        <x-bladewind::input
                            name="password"
                            label="Password"
                            type="password"
                            viewable="true"
                            required="true"
                        />
                    </div>
                    <div>
                        <x-bladewind::input
                            name="password_confirmation"
                            label="Confirm password"
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
                        class="mt-3 shadow-md hover:shadow-blue-500/50">
                        Register
                    </x-bladewind::button>
                    <x-bladewind::button
                        name="btn-clear"
                        type="secondary"
                        class="ml-2 mt-3 shadow-md hover:shadow-slate-500/50"
                        id="clear-all">
                        Clear All
                    </x-bladewind::button>

                </div>
            </form>
        </div>
    @endsection

    @section('custom-js')
        <script>
            $(document).ready(function() {
                $('#clear-all').on('click', function(event) {
                    event.preventDefault();
                    $('#register-form').find('input').val('');
                });

                $('#register-form').on('submit', function(event) {
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
                            $('.text-red-500').remove();
                            $('input').removeClass('border-red-400');

                            if(response.success){
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
                                    title: "Account created successfully. Redirecting to home page..."
                                });
                                setTimeout(function () {
                                    window.location.replace('/');
                                }, 2000);
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to register account. Please try again.',
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

                                $('[name="password"]').val('');
                                $('[name="password_confirmation"]').val('');

                                for (let key in errors) {
                                    let input = $('[name=' + key + ']');
                                    input.addClass('border-red-400');
                                    input.after('<p class="text-red-500 text-sm mb-2">' + errors[key][0] + '</p>');
                                }
                            }else if(xhr.status === 0){
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to register account</br>Please try again.',
                                    text: `Error ${xhr.status}: No internet connection or Server is down.`
                                });
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to register account</br>Please try again.',
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




