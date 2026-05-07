<?php require_once '../includes/header.php'; ?>

<!-- Contenido del Dashboard -->
<div class="space-y-6">

    <!-- Título de página -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Dashboard</h3>
            <p class="mt-1 text-sm text-gray-500">Resumen general del sistema</p>
        </div>
        <div class="px-4 py-2 text-sm text-gray-500 bg-white rounded-lg shadow-sm">
            <i class="mr-2 fa-regular fa-calendar"></i>
            <span id="fecha-hoy"></span>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">

        <!-- Total Pacientes -->
        <div class="flex items-center gap-4 p-6 bg-white border-l-4 border-blue-500 shadow-sm rounded-xl">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full">
                <i class="text-xl text-blue-600 fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Pacientes</p>
                <p class="text-2xl font-bold text-gray-800" id="total-pacientes">...</p>
            </div>
        </div>

        <!-- Citas Hoy -->
        <div class="flex items-center gap-4 p-6 bg-white border-l-4 shadow-sm rounded-xl border-cyan-500">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-cyan-100">
                <i class="text-xl fa-solid fa-calendar-day text-cyan-600"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Citas Hoy</p>
                <p class="text-2xl font-bold text-gray-800" id="citas-hoy">...</p>
            </div>
        </div>

        <!-- Citas Pendientes -->
        <div class="flex items-center gap-4 p-6 bg-white border-l-4 border-yellow-500 shadow-sm rounded-xl">
            <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-full">
                <i class="text-xl text-yellow-600 fa-solid fa-clock"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Citas Pendientes</p>
                <p class="text-2xl font-bold text-gray-800" id="citas-pendientes">...</p>
            </div>
        </div>

        <!-- Total Médicos -->
        <div class="flex items-center gap-4 p-6 bg-white border-l-4 border-green-500 shadow-sm rounded-xl">
            <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-full">
                <i class="text-xl text-green-600 fa-solid fa-user-doctor"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Médicos</p>
                <p class="text-2xl font-bold text-gray-800" id="total-medicos">...</p>
            </div>
        </div>

    </div>

    <!-- Fila 2: Citas de hoy + Últimos pacientes -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <!-- Citas de hoy -->
        <div class="p-6 bg-white shadow-sm rounded-xl">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-semibold text-gray-800">
                    <i class="mr-2 text-blue-600 fa-solid fa-calendar-check"></i>
                    Citas de Hoy
                </h4>
                <a href="citas.php" class="text-sm text-blue-600 hover:underline">Ver todas</a>
            </div>
            <div id="lista-citas-hoy">
                <div class="py-8 text-center text-gray-400">
                    <i class="text-2xl fa-solid fa-spinner fa-spin"></i>
                    <p class="mt-2 text-sm">Cargando citas...</p>
                </div>
            </div>
        </div>

        <!-- Últimos pacientes registrados -->
        <div class="p-6 bg-white shadow-sm rounded-xl">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-semibold text-gray-800">
                    <i class="mr-2 fa-solid fa-user-plus text-cyan-600"></i>
                    Últimos Pacientes
                </h4>
                <a href="pacientes.php" class="text-sm text-blue-600 hover:underline">Ver todos</a>
            </div>
            <div id="lista-ultimos-pacientes">
                <div class="py-8 text-center text-gray-400">
                    <i class="text-2xl fa-solid fa-spinner fa-spin"></i>
                    <p class="mt-2 text-sm">Cargando pacientes...</p>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    $(document).ready(function() {

        // Mostrar fecha de hoy
        const hoy = new Date();
        const opciones = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        $('#fecha-hoy').text(hoy.toLocaleDateString('es-CL', opciones));

        // Cambiar título del header
        $('#page-title').text('Dashboard');

        // Cargar estadísticas
        $.ajax({
            url: '../api/dashboard.php',
            method: 'GET',
            success: function(res) {
                if (res.success) {
                    $('#total-pacientes').text(res.data.total_pacientes);
                    $('#citas-hoy').text(res.data.citas_hoy);
                    $('#citas-pendientes').text(res.data.citas_pendientes);
                    $('#total-medicos').text(res.data.total_medicos);

                    // Renderizar citas de hoy
                    if (res.data.citas_del_dia.length > 0) {
                        let htmlCitas = '';
                        res.data.citas_del_dia.forEach(cita => {
                            const estadoColor = {
                                'pendiente': 'bg-yellow-100 text-yellow-700',
                                'confirmada': 'bg-green-100 text-green-700',
                                'cancelada': 'bg-red-100 text-red-700',
                                'completada': 'bg-blue-100 text-blue-700'
                            };
                            htmlCitas += `
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full">
                                        <i class="text-xs text-blue-600 fa-solid fa-user"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">${cita.paciente}</p>
                                        <p class="text-xs text-gray-500">${cita.hora} — Dr. ${cita.medico}</p>
                                    </div>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full font-medium ${estadoColor[cita.estado] || 'bg-gray-100 text-gray-600'}">
                                    ${cita.estado}
                                </span>
                            </div>`;
                        });
                        $('#lista-citas-hoy').html(htmlCitas);
                    } else {
                        $('#lista-citas-hoy').html('<p class="py-8 text-sm text-center text-gray-400">No hay citas programadas para hoy</p>');
                    }

                    // Renderizar últimos pacientes
                    if (res.data.ultimos_pacientes.length > 0) {
                        let htmlPacientes = '';
                        res.data.ultimos_pacientes.forEach(p => {
                            htmlPacientes += `
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-cyan-100">
                                        <i class="text-xs fa-solid fa-user text-cyan-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">${p.nombre} ${p.apellido}</p>
                                        <p class="text-xs text-gray-500">${p.rut}</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400">${p.fecha}</span>
                            </div>`;
                        });
                        $('#lista-ultimos-pacientes').html(htmlPacientes);
                    } else {
                        $('#lista-ultimos-pacientes').html('<p class="py-8 text-sm text-center text-gray-400">No hay pacientes registrados aún</p>');
                    }
                }
            },
            error: function() {
                console.log('Error al cargar estadísticas');
            }
        });

    });
</script>

<?php require_once '../includes/footer.php'; ?>