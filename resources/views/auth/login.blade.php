<x-layout>
    @section('custom-css')
    @endsection

    @section('title', 'Login')

    @section('heading')
        <div class="flex pb-3 justify-center">
            Login Account
        </div>
    @endsection

    @section('content')
        <div class="w-96 place-self-center border-2 bg-white p-7 mt-2 rounded-2xl">
            <form id="login-form" method="POST" action="/login">
                @csrf
                <div>
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
                            name="password"
                            label="Password"
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
                        Login
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
                    $('#login-form').find('input').val('');
                });

                $('#login-form').on('submit', function(event) {
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
                                    title: "Logged in successfully. Redirecting to home page..."
                                });
                                setTimeout(function () {
                                    window.location.replace('/');
                                }, 2000);
                            }else {
                                $('[name="password"]').val('');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Login Failed',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            let errors = xhr.responseJSON.errors;

                            $('.text-red-500').remove();
                            $('input').removeClass('border-red-400');

                            $('[name="password"]').val('');

                            for (let key in errors) {
                                let input = $('[name=' + key + ']');
                                input.addClass('border-red-400');
                                input.after('<p class="text-red-500 text-sm mb-2">' + errors[key][0] + '</p>');
                            }
                        }
                    });
                });
            });
        </script>
    @endsection
</x-layout>
