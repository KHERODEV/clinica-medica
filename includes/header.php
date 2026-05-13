<?php
require_once __DIR__ . '/auth.php';
verificarSesion();
$usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';
$usuario_rol = $_SESSION['usuario_rol'] ?? '';
$usuario_email = $_SESSION['usuario_email'] ?? '';

// Página activa para el menú
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Médica — Sistema de Gestión</title>
    <link rel="stylesheet" href="../assets/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body class="font-sans bg-gray-50">

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-0 left-0 z-50 flex flex-col w-64 h-full text-white transition-all duration-300 bg-blue-900">

        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 py-5 border-b border-blue-800">
            <div class="flex items-center justify-center w-10 h-10 bg-white rounded-full">
                <i class="text-xl text-blue-700 fa-solid fa-hospital"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold leading-tight">Clínica Médica</h1>
                <p class="text-xs text-blue-300">Sistema de Gestión</p>
            </div>
        </div>

        <!-- Usuario -->
        <div class="px-6 py-4 border-b border-blue-800">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center bg-blue-600 rounded-full w-9 h-9">
                    <i class="text-sm fa-solid fa-user"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold"><?= htmlspecialchars($usuario_nombre) ?></p>
                    <p class="text-xs text-blue-300 capitalize"><?= htmlspecialchars($usuario_rol) ?></p>
                </div>
            </div>
        </div>

        <!-- Menú de navegación -->
        <nav class="flex-1 px-4 py-4 overflow-y-auto">
            <p class="px-2 mb-3 text-xs font-semibold tracking-wider text-blue-400 uppercase">Menú Principal</p>

            <a href="dashboard.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 transition <?= $pagina_actual === 'dashboard.php' ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' ?>">
                <i class="w-5 text-center fa-solid fa-gauge-high"></i>
                <span>Dashboard</span>
            </a>

            <a href="pacientes.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 transition <?= $pagina_actual === 'pacientes.php' ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' ?>">
                <i class="w-5 text-center fa-solid fa-users"></i>
                <span>Pacientes</span>
            </a>

            <a href="citas.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 transition <?= $pagina_actual === 'citas.php' ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' ?>">
                <i class="w-5 text-center fa-solid fa-calendar-days"></i>
                <span>Citas</span>
            </a>

            <a href="historial.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 transition <?= $pagina_actual === 'historial.php' ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' ?>">
                <i class="w-5 text-center fa-solid fa-file-medical"></i>
                <span>Historial Médico</span>
            </a>

            <a href="recetas.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 transition <?= $pagina_actual === 'recetas.php' ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' ?>">
                <i class="w-5 text-center fa-solid fa-prescription"></i>
                <span>Recetas</span>
            </a>

            <?php if ($usuario_rol === 'admin'): ?>
                <div class="mt-4">
                    <p class="px-2 mb-3 text-xs font-semibold tracking-wider text-blue-400 uppercase">Administración</p>
                    <a href="usuarios.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 transition <?= $pagina_actual === 'usuarios.php' ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800 hover:text-white' ?>">
                        <i class="w-5 text-center fa-solid fa-user-doctor"></i>
                        <span>Usuarios</span>
                    </a>
                </div>
            <?php endif; ?>
        </nav>

        <!-- Logout -->
        <div class="px-4 py-4 border-t border-blue-800">
            <a href="../api/logout.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-200 hover:bg-red-600 hover:text-white transition">
                <i class="w-5 text-center fa-solid fa-right-from-bracket"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>

    </aside>

    <!-- Contenido principal -->
    <div class="flex flex-col min-h-screen ml-64">

        <!-- Header superior -->
        <header class="sticky top-0 z-40 flex items-center justify-between px-6 py-4 bg-white shadow-sm">
            <div class="flex items-center gap-3">
                <button id="toggle-sidebar" class="text-gray-500 transition hover:text-blue-700">
                    <i class="text-xl fa-solid fa-bars"></i>
                </button>
                <h2 id="page-title" class="text-lg font-semibold text-gray-700">Dashboard</h2>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">
                    <i class="mr-1 fa-regular fa-clock"></i>
                    <span id="reloj"></span>
                </span>
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-8 h-8 bg-blue-700 rounded-full">
                        <i class="text-xs text-white fa-solid fa-user"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($usuario_nombre) ?></span>
                </div>
            </div>
        </header>

        <!-- Área de contenido -->
        <main class="flex-1 p-6">