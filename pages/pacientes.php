<?php require_once '../includes/header.php'; ?>

<div class="space-y-6">

    <!-- Título y botón agregar -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Pacientes</h3>
            <p class="mt-1 text-sm text-gray-500">Gestión de pacientes registrados</p>
        </div>
        <button id="btn-nuevo-paciente" class="flex items-center gap-2 px-4 py-2 text-white transition bg-blue-700 rounded-lg shadow-sm hover:bg-blue-800">
            <i class="fa-solid fa-plus"></i>
            <span>Nuevo Paciente</span>
        </button>
    </div>

    <!-- Buscador -->
    <div class="p-4 bg-white shadow-sm rounded-xl">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid fa-search"></i>
            </span>
            <input
                type="text"
                id="buscador"
                placeholder="Buscar por nombre, apellido o RUT..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
        </div>
    </div>

    <!-- Tabla de pacientes -->
    <div class="overflow-hidden bg-white shadow-sm rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Paciente</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">RUT</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Teléfono</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Email</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Previsión</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Estado</th>
                        <th class="px-6 py-3 font-semibold text-center text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-pacientes">
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">
                            <i class="text-2xl fa-solid fa-spinner fa-spin"></i>
                            <p class="mt-2">Cargando pacientes...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Paginación -->
        <div class="flex items-center justify-between px-6 py-3 border-t border-gray-200">
            <p class="text-sm text-gray-500" id="info-paginacion">Cargando...</p>
            <div class="flex gap-2" id="controles-paginacion"></div>
        </div>
    </div>

</div>

<!-- Modal Nuevo/Editar Paciente -->
<div id="modal-paciente" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black bg-opacity-50">
    <div class="w-full max-w-2xl max-h-screen overflow-y-auto bg-white shadow-2xl rounded-2xl">

        <!-- Header del modal -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h4 class="text-lg font-bold text-gray-800" id="modal-titulo">Nuevo Paciente</h4>
            <button id="btn-cerrar-modal" class="text-gray-400 transition hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Formulario -->
        <div class="px-6 py-4 space-y-4">
            <input type="hidden" id="paciente-id" />

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Nombre *</label>
                    <input type="text" id="paciente-nombre" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Nombre" />
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Apellido *</label>
                    <input type="text" id="paciente-apellido" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Apellido" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">RUT *</label>
                    <input type="text" id="paciente-rut" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="12.345.678-9" />
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Fecha de Nacimiento *</label>
                    <input type="date" id="paciente-fecha-nacimiento" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Género</label>
                    <select id="paciente-genero" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">Seleccionar...</option>
                        <option value="masculino">Masculino</option>
                        <option value="femenino">Femenino</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Previsión</label>
                    <select id="paciente-prevision" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">Seleccionar...</option>
                        <option value="Fonasa">Fonasa</option>
                        <option value="Isapre">Isapre</option>
                        <option value="Particular">Particular</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text" id="paciente-telefono" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="+56 9 1234 5678" />
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="paciente-email" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="correo@ejemplo.com" />
                </div>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Dirección</label>
                <input type="text" id="paciente-direccion" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Calle, número, ciudad" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Contacto de Emergencia</label>
                    <input type="text" id="paciente-contacto-emergencia" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Nombre del contacto" />
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Teléfono de Emergencia</label>
                    <input type="text" id="paciente-telefono-emergencia" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="+56 9 1234 5678" />
                </div>
            </div>

            <!-- Alerta -->
            <div id="modal-alerta" class="hidden px-4 py-3 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50"></div>
        </div>

        <!-- Footer del modal -->
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

        $('#page-title').text('Pacientes');

        let paginaActual = 1;
        const porPagina = 10;
        let totalPacientes = 0;
        let busqueda = '';

        // Cargar pacientes
        function cargarPacientes() {
            $('#tabla-pacientes').html(`
            <tr><td colspan="7" class="py-8 text-center text-gray-400">
                <i class="text-2xl fa-solid fa-spinner fa-spin"></i>
                <p class="mt-2">Cargando pacientes...</p>
            </td></tr>
        `);

            $.ajax({
                url: '../api/pacientes.php',
                method: 'GET',
                data: {
                    pagina: paginaActual,
                    por_pagina: porPagina,
                    busqueda: busqueda
                },
                success: function(res) {
                    if (res.success) {
                        totalPacientes = res.total;
                        renderTabla(res.data);
                        renderPaginacion();
                    }
                }
            });
        }

        // Renderizar tabla
        function renderTabla(pacientes) {
            if (pacientes.length === 0) {
                $('#tabla-pacientes').html(`
                <tr><td colspan="7" class="py-8 text-center text-gray-400">
                    <i class="mb-2 text-3xl fa-solid fa-users"></i>
                    <p>No se encontraron pacientes</p>
                </td></tr>
            `);
                return;
            }

            let html = '';
            pacientes.forEach(p => {
                html += `
                <tr class="transition border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full">
                                <i class="text-xs text-blue-600 fa-solid fa-user"></i>
                            </div>
                            <span class="font-medium text-gray-800">${p.nombre} ${p.apellido}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">${p.rut}</td>
                    <td class="px-6 py-4 text-gray-600">${p.telefono || '—'}</td>
                    <td class="px-6 py-4 text-gray-600">${p.email || '—'}</td>
                    <td class="px-6 py-4 text-gray-600">${p.prevision || '—'}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium ${p.activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                            ${p.activo ? 'Activo' : 'Inactivo'}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="editarPaciente('${p.id}')" class="text-blue-600 transition hover:text-blue-800" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button onclick="eliminarPaciente('${p.id}', '${p.nombre} ${p.apellido}')" class="text-red-500 transition hover:text-red-700" title="Eliminar">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            });
            $('#tabla-pacientes').html(html);
        }

        // Paginación
        function renderPaginacion() {
            const totalPaginas = Math.ceil(totalPacientes / porPagina);
            const inicio = (paginaActual - 1) * porPagina + 1;
            const fin = Math.min(paginaActual * porPagina, totalPacientes);

            $('#info-paginacion').text(totalPacientes > 0 ? `Mostrando ${inicio}-${fin} de ${totalPacientes} pacientes` : 'Sin resultados');

            let html = '';
            for (let i = 1; i <= totalPaginas; i++) {
                html += `<button onclick="cambiarPagina(${i})" class="px-3 py-1 rounded-lg text-sm ${i === paginaActual ? 'bg-blue-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}">${i}</button>`;
            }
            $('#controles-paginacion').html(html);
        }

        // Buscador
        let timeout;
        $('#buscador').on('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                busqueda = $(this).val();
                paginaActual = 1;
                cargarPacientes();
            }, 400);
        });

        // Abrir modal nuevo
        $('#btn-nuevo-paciente').click(function() {
            limpiarModal();
            $('#modal-titulo').text('Nuevo Paciente');
            $('#modal-paciente').removeClass('hidden');
        });

        // Cerrar modal
        $('#btn-cerrar-modal, #btn-cancelar').click(function() {
            $('#modal-paciente').addClass('hidden');
        });

        // Guardar paciente
        $('#btn-guardar').click(function() {
            const id = $('#paciente-id').val();
            const datos = {
                nombre: $('#paciente-nombre').val().trim(),
                apellido: $('#paciente-apellido').val().trim(),
                rut: $('#paciente-rut').val().trim(),
                fecha_nacimiento: $('#paciente-fecha-nacimiento').val(),
                genero: $('#paciente-genero').val(),
                telefono: $('#paciente-telefono').val().trim(),
                email: $('#paciente-email').val().trim(),
                direccion: $('#paciente-direccion').val().trim(),
                prevision: $('#paciente-prevision').val(),
                contacto_emergencia: $('#paciente-contacto-emergencia').val().trim(),
                telefono_emergencia: $('#paciente-telefono-emergencia').val().trim()
            };

            if (!datos.nombre || !datos.apellido || !datos.rut || !datos.fecha_nacimiento) {
                $('#modal-alerta').text('Por favor completa los campos obligatorios (*)').removeClass('hidden');
                return;
            }

            if (id) datos.id = id;

            $('#btn-guardar').html('<i class="fa-solid fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);

            $.ajax({
                url: '../api/pacientes.php',
                method: id ? 'PUT' : 'POST',
                contentType: 'application/json',
                data: JSON.stringify(datos),
                success: function(res) {
                    if (res.success) {
                        $('#modal-paciente').addClass('hidden');
                        cargarPacientes();
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
            $('#paciente-id, #paciente-nombre, #paciente-apellido, #paciente-rut, #paciente-fecha-nacimiento, #paciente-telefono, #paciente-email, #paciente-direccion, #paciente-contacto-emergencia, #paciente-telefono-emergencia').val('');
            $('#paciente-genero, #paciente-prevision').val('');
            $('#modal-alerta').addClass('hidden');
        }

        cargarPacientes();
    });

    // Editar paciente
    function editarPaciente(id) {
        $.ajax({
            url: '../api/pacientes.php',
            method: 'GET',
            data: {
                id: id
            },
            success: function(res) {
                if (res.success) {
                    const p = res.data;
                    $('#paciente-id').val(p.id);
                    $('#paciente-nombre').val(p.nombre);
                    $('#paciente-apellido').val(p.apellido);
                    $('#paciente-rut').val(p.rut);
                    $('#paciente-fecha-nacimiento').val(p.fecha_nacimiento);
                    $('#paciente-genero').val(p.genero);
                    $('#paciente-telefono').val(p.telefono);
                    $('#paciente-email').val(p.email);
                    $('#paciente-direccion').val(p.direccion);
                    $('#paciente-prevision').val(p.prevision);
                    $('#paciente-contacto-emergencia').val(p.contacto_emergencia);
                    $('#paciente-telefono-emergencia').val(p.telefono_emergencia);
                    $('#modal-titulo').text('Editar Paciente');
                    $('#modal-paciente').removeClass('hidden');
                }
            }
        });
    }

    // Eliminar paciente
    function eliminarPaciente(id, nombre) {
        if (!confirm(`¿Estás segura de eliminar al paciente ${nombre}?`)) return;
        $.ajax({
            url: '../api/pacientes.php',
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
        cargarPacientes();
    }
</script>

<?php require_once '../includes/footer.php'; ?>