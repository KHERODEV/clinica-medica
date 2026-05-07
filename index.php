<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Médica — Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-cyan-700">

    <div class="w-full max-w-md px-6">

        <!-- Logo y título -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 mb-4 bg-white rounded-full shadow-lg">
                <i class="text-4xl text-blue-700 fa-solid fa-hospital"></i>
            </div>
            <h1 class="text-3xl font-bold text-white">Clínica Médica</h1>
            <p class="mt-1 text-blue-200">Sistema de Gestión</p>
        </div>

        <!-- Tarjeta de login -->
        <div class="p-8 bg-white shadow-2xl rounded-2xl">
            <h2 class="mb-2 text-2xl font-bold text-gray-800">Bienvenido</h2>
            <p class="mb-6 text-sm text-gray-500">Ingresa tus credenciales para continuar</p>

            <!-- Alerta de error -->
            <div id="alerta-error" class="flex items-center hidden gap-2 px-4 py-3 mb-5 text-red-700 border border-red-200 rounded-lg bg-red-50">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span id="mensaje-error">Credenciales incorrectas</span>
            </div>

            <!-- Formulario -->
            <div id="form-login">

                <div class="mb-5">
                    <label class="block mb-1 text-sm font-medium text-gray-700">
                        Correo electrónico
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input
                            type="email"
                            id="email"
                            placeholder="correo@ejemplo.com"
                            class="w-full py-3 pl-10 pr-4 text-gray-800 transition border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block mb-1 text-sm font-medium text-gray-700">
                        Contraseña
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input
                            type="password"
                            id="password"
                            placeholder="••••••••"
                            class="w-full py-3 pl-10 pr-10 text-gray-800 transition border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button
                    id="btn-login"
                    class="flex items-center justify-center w-full gap-2 py-3 font-semibold text-white transition duration-200 bg-blue-700 rounded-lg shadow-md hover:bg-blue-800">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Iniciar Sesión</span>
                </button>

            </div>
        </div>

        <p class="mt-6 text-sm text-center text-blue-200">
            © 2026 Clínica Médica — Todos los derechos reservados
        </p>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {

            $('#toggle-password').click(function() {
                const input = $('#password');
                const icon = $(this).find('i');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            $('#btn-login').click(function() {
                const email = $('#email').val().trim();
                const password = $('#password').val().trim();

                if (!email || !password) {
                    mostrarError('Por favor completa todos los campos');
                    return;
                }

                $('#btn-login').html('<i class="fa-solid fa-spinner fa-spin"></i> Ingresando...').prop('disabled', true);

                $.ajax({
                    url: 'api/auth.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        email,
                        password
                    }),
                    success: function(res) {
                        if (res.success) {
                            $('#btn-login').html('<i class="fa-solid fa-check"></i> Redirigiendo...');
                            window.location.href = 'pages/dashboard.php';
                        } else {
                            mostrarError(res.message || 'Credenciales incorrectas');
                            resetBtn();
                        }
                    },
                    error: function() {
                        mostrarError('Error de conexión, intenta nuevamente');
                        resetBtn();
                    }
                });
            });

            $('#password').keypress(function(e) {
                if (e.which === 13) $('#btn-login').click();
            });

            function mostrarError(mensaje) {
                $('#mensaje-error').text(mensaje);
                $('#alerta-error').removeClass('hidden');
                setTimeout(() => $('#alerta-error').addClass('hidden'), 4000);
            }

            function resetBtn() {
                $('#btn-login').html('<i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión').prop('disabled', false);
            }

        });
    </script>
</body>

</html>