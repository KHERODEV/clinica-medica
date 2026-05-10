<?php require_once '../includes/header.php'; ?>

<div class="space-y-6">

    <!-- Título y botón agregar -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Citas</h3>
            <p class="mt-1 text-sm text-gray-500">Gestión de citas médicas</p>
        </div>
        <button id="btn-nueva-cita" class="flex items-center gap-2 px-4 py-2 text-white transition bg-blue-700 rounded-lg shadow-sm hover:bg-blue-800">
            <i class="fa-solid fa-plus"></i>
            <span>Nueva Cita</span>
        </button>
    </div>

    <!-- Filtros -->
    <div class="grid grid-cols-1 gap-4 p-4 bg-white shadow-sm rounded-xl md:grid-cols-4">
        <div class="relative md:col-span-2">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid fa-search"></i>
            </span>
            <input type="text" id="buscador" placeholder="Buscar por paciente o médico..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
        </div>
        <select id="filtro-estado" class="border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            <option value="">Todos los estados</option>
            <option value="pendiente">Pendiente</option>
            <option value="confirmada">Confirmada</option>
            <option value="completada">Completada</option>
            <option value="cancelada">Cancelada</option>
        </select>
        <input type="date" id="filtro-fecha" class="border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
    </div>

    <!-- Tabla de citas -->
    <div class="overflow-hidden bg-white shadow-sm rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Paciente</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Médico</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Fecha</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Hora</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Motivo</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Estado</th>
                        <th class="px-6 py-3 font-semibold text-center text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-citas">
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">
                            <i class="text-2xl fa-solid fa-spinner fa-spin"></i>
                            <p class="mt-2">Cargando citas...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between px-6 py-3 border-t border-gray-200">
            <p class="text-sm text-gray-500" id="info-paginacion">Cargando...</p>
            <div class="flex gap-2" id="controles-paginacion"></div>
        </div>
    </div>

</div>

<!-- Modal Nueva/Editar Cita -->
<div id="modal-cita" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black bg-opacity-50">
    <div class="w-full max-w-lg max-h-screen overflow-y-auto bg-white shadow-2xl rounded-2xl">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h4 class="text-lg font-bold text-gray-800" id="modal-titulo">Nueva Cita</h4>
            <button id="btn-cerrar-modal" class="text-gray-400 transition hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="px-6 py-4 space-y-4">
            <input type="hidden" id="cita-id" />

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Paciente *</label>
                <select id="cita-paciente" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Seleccionar paciente...</option>
                </select>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Médico *</label>
                <select id="cita-medico" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Seleccionar médico...</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Fecha *</label>
                    <input type="date" id="cita-fecha" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Hora *</label>
                    <input type="time" id="cita-hora" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                </div>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Motivo</label>
                <input type="text" id="cita-motivo" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Motivo de la consulta" />
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Estado</label>
                <select id="cita-estado" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="pendiente">Pendiente</option>
                    <option value="confirmada">Confirmada</option>
                    <option value="completada">Completada</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Observaciones</label>
                <textarea id="cita-observaciones" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Observaciones adicionales..."></textarea>
            </div>

            <div id="modal-alerta" class="hidden px-4 py-3 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50"></div>
        </div>

        <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200">
            <button id="btn-cancelar" class="px-4 py-2 text-gray-700 transition border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancelar
            </button>
            <button id="btn-guardar" class="flex items-center gap-2 px-4 py-2 text-white transition bg-blue-700 rounded-lg hover:bg-blue-800">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Guardar</span>
            </button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        $('#page-title').text('Citas');

        let paginaActual = 1;
        const porPagina = 10;
        let totalCitas = 0;
        let busqueda = '';
        let filtroEstado = '';
        let filtroFecha = '';

        // Cargar pacientes y médicos para el modal
        function cargarSelects() {
            $.ajax({
                url: '../api/citas.php',
                method: 'GET',
                data: {
                    action: 'selects'
                },
                success: function(res) {
                    if (res.success) {
                        res.pacientes.forEach(p => {
                            $('#cita-paciente').append(`<option value="${p.id}">${p.nombre} ${p.apellido} — ${p.rut}</option>`);
                        });
                        res.medicos.forEach(m => {
                            $('#cita-medico').append(`<option value="${m.id}">Dr. ${m.nombre} ${m.apellido}</option>`);
                        });
                    }
                }
            });
        }

        // Cargar citas
        function cargarCitas() {
            $('#tabla-citas').html(`
            <tr><td colspan="7" class="py-8 text-center text-gray-400">
                <i class="text-2xl fa-solid fa-spinner fa-spin"></i>
                <p class="mt-2">Cargando citas...</p>
            </td></tr>
        `);

            $.ajax({
                url: '../api/citas.php',
                method: 'GET',
                data: {
                    pagina: paginaActual,
                    por_pagina: porPagina,
                    busqueda,
                    estado: filtroEstado,
                    fecha: filtroFecha
                },
                success: function(res) {
                    if (res.success) {
                        totalCitas = res.total;
                        renderTabla(res.data);
                        renderPaginacion();
                    }
                }
            });
        }

        // Renderizar tabla
        function renderTabla(citas) {
            if (citas.length === 0) {
                $('#tabla-citas').html(`
                <tr><td colspan="7" class="py-8 text-center text-gray-400">
                    <i class="mb-2 text-3xl fa-solid fa-calendar-xmark"></i>
                    <p>No se encontraron citas</p>
                </td></tr>
            `);
                return;
            }

            const estadoColor = {
                'pendiente': 'bg-yellow-100 text-yellow-700',
                'confirmada': 'bg-green-100 text-green-700',
                'completada': 'bg-blue-100 text-blue-700',
                'cancelada': 'bg-red-100 text-red-700'
            };

            let html = '';
            citas.forEach(c => {
                html += `
                <tr class="transition border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-800">${c.paciente}</td>
                    <td class="px-6 py-4 text-gray-600">Dr. ${c.medico}</td>
                    <td class="px-6 py-4 text-gray-600">${c.fecha}</td>
                    <td class="px-6 py-4 text-gray-600">${c.hora}</td>
                    <td class="px-6 py-4 text-gray-600">${c.motivo || '—'}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium ${estadoColor[c.estado] || 'bg-gray-100 text-gray-600'}">
                            ${c.estado}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="editarCita('${c.id}')" class="text-blue-600 transition hover:text-blue-800" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button onclick="eliminarCita('${c.id}')" class="text-red-500 transition hover:text-red-700" title="Eliminar">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            });
            $('#tabla-citas').html(html);
        }

        // Paginación
        function renderPaginacion() {
            const totalPaginas = Math.ceil(totalCitas / porPagina);
            const inicio = totalCitas > 0 ? (paginaActual - 1) * porPagina + 1 : 0;
            const fin = Math.min(paginaActual * porPagina, totalCitas);
            $('#info-paginacion').text(totalCitas > 0 ? `Mostrando ${inicio}-${fin} de ${totalCitas} citas` : 'Sin resultados');
            let html = '';
            for (let i = 1; i <= totalPaginas; i++) {
                html += `<button onclick="cambiarPagina(${i})" class="px-3 py-1 rounded-lg text-sm ${i === paginaActual ? 'bg-blue-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}">${i}</button>`;
            }
            $('#controles-paginacion').html(html);
        }

        // Filtros
        let timeout;
        $('#buscador').on('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                busqueda = $(this).val();
                paginaActual = 1;
                cargarCitas();
            }, 400);
        });
        $('#filtro-estado').change(function() {
            filtroEstado = $(this).val();
            paginaActual = 1;
            cargarCitas();
        });
        $('#filtro-fecha').change(function() {
            filtroFecha = $(this).val();
            paginaActual = 1;
            cargarCitas();
        });

        // Abrir modal nueva cita
        $('#btn-nueva-cita').click(function() {
            limpiarModal();
            $('#modal-titulo').text('Nueva Cita');
            $('#modal-cita').removeClass('hidden');
        });

        // Cerrar modal
        $('#btn-cerrar-modal, #btn-cancelar').click(function() {
            $('#modal-cita').addClass('hidden');
        });

        // Guardar cita
        $('#btn-guardar').click(function() {
            const id = $('#cita-id').val();
            const datos = {
                paciente_id: $('#cita-paciente').val(),
                medico_id: $('#cita-medico').val(),
                fecha: $('#cita-fecha').val(),
                hora: $('#cita-hora').val(),
                motivo: $('#cita-motivo').val().trim(),
                estado: $('#cita-estado').val(),
                observaciones: $('#cita-observaciones').val().trim()
            };

            if (!datos.paciente_id || !datos.medico_id || !datos.fecha || !datos.hora) {
                $('#modal-alerta').text('Por favor completa los campos obligatorios (*)').removeClass('hidden');
                return;
            }

            if (id) datos.id = id;

            $('#btn-guardar').html('<i class="fa-solid fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);

            $.ajax({
                url: '../api/citas.php',
                method: id ? 'PUT' : 'POST',
                contentType: 'application/json',
                data: JSON.stringify(datos),
                success: function(res) {
                    if (res.success) {
                        $('#modal-cita').addClass('hidden');
                        cargarCitas();
                    } else {
                        $('#modal-alerta').text(res.message).removeClass('hidden');
                    }
                },
                error: function() {
                    $('#modal-alerta').text('Error al guardar, intenta nuevamente').removeClass('hidden');
                },
                complete: function() {
                    $('#btn-guardar').html('<i class="fa-solid fa-floppy-disk"></i> Guardar').prop('disabled', false);
                }
            });
        });

        function limpiarModal() {
            $('#cita-id, #cita-motivo, #cita-observaciones').val('');
            $('#cita-paciente, #cita-medico').val('');
            $('#cita-fecha, #cita-hora').val('');
            $('#cita-estado').val('pendiente');
            $('#modal-alerta').addClass('hidden');
        }

        cargarSelects();
        cargarCitas();
    });

    function editarCita(id) {
        $.ajax({
            url: '../api/citas.php',
            method: 'GET',
            data: {
                id: id
            },
            success: function(res) {
                if (res.success) {
                    const c = res.data;
                    $('#cita-id').val(c.id);
                    $('#cita-paciente').val(c.paciente_id);
                    $('#cita-medico').val(c.medico_id);
                    $('#cita-fecha').val(c.fecha);
                    $('#cita-hora').val(c.hora);
                    $('#cita-motivo').val(c.motivo);
                    $('#cita-estado').val(c.estado);
                    $('#cita-observaciones').val(c.observaciones);
                    $('#modal-titulo').text('Editar Cita');
                    $('#modal-cita').removeClass('hidden');
                }
            }
        });
    }

    function eliminarCita(id) {
        if (!confirm('¿Estás segura de eliminar esta cita?')) return;
        $.ajax({
            url: '../api/citas.php',
            method: 'DELETE',
            contentType: 'application/json',
            data: JSON.stringify({
                id: id
            }),
            success: function(res) {
                if (res.success) location.reload();
            }
        });
    }

    function cambiarPagina(pagina) {
        paginaActual = pagina;
        cargarCitas();
    }
</script>

<?php require_once '../includes/footer.php'; ?>