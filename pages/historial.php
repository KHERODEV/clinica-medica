<?php require_once '../includes/header.php'; ?>

<div class="space-y-6">

    <!-- Título -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Historial Médico</h3>
            <p class="mt-1 text-sm text-gray-500">Registro de diagnósticos y tratamientos</p>
        </div>
        <button id="btn-nuevo-historial" class="flex items-center gap-2 px-4 py-2 text-white transition bg-blue-700 rounded-lg shadow-sm hover:bg-blue-800">
            <i class="fa-solid fa-plus"></i>
            <span>Nuevo Registro</span>
        </button>
    </div>

    <!-- Buscador -->
    <div class="grid grid-cols-1 gap-4 p-4 bg-white shadow-sm rounded-xl md:grid-cols-3">
        <div class="relative md:col-span-2">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid fa-search"></i>
            </span>
            <input type="text" id="buscador" placeholder="Buscar por paciente o diagnóstico..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
        </div>
        <input type="date" id="filtro-fecha" class="border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
    </div>

    <!-- Tabla -->
    <div class="overflow-hidden bg-white shadow-sm rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Paciente</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Médico</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Diagnóstico</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Tratamiento</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Fecha</th>
                        <th class="px-6 py-3 font-semibold text-center text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-historial">
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400">
                            <i class="text-2xl fa-solid fa-spinner fa-spin"></i>
                            <p class="mt-2">Cargando historial...</p>
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

<!-- Modal Nuevo/Editar Historial -->
<div id="modal-historial" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black bg-opacity-50">
    <div class="w-full max-w-2xl max-h-screen overflow-y-auto bg-white shadow-2xl rounded-2xl">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h4 class="text-lg font-bold text-gray-800" id="modal-titulo">Nuevo Registro</h4>
            <button id="btn-cerrar-modal" class="text-gray-400 transition hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="px-6 py-4 space-y-4">
            <input type="hidden" id="historial-id" />

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Paciente *</label>
                    <select id="historial-paciente" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">Seleccionar paciente...</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Médico *</label>
                    <select id="historial-medico" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">Seleccionar médico...</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Fecha *</label>
                <input type="date" id="historial-fecha" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Síntomas</label>
                <textarea id="historial-sintomas" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Síntomas reportados por el paciente..."></textarea>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Diagnóstico *</label>
                <textarea id="historial-diagnostico" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Diagnóstico médico..."></textarea>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Tratamiento</label>
                <textarea id="historial-tratamiento" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Tratamiento indicado..."></textarea>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Observaciones</label>
                <textarea id="historial-observaciones" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Observaciones adicionales..."></textarea>
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

        $('#page-title').text('Historial Médico');

        let paginaActual = 1;
        const porPagina = 10;
        let totalRegistros = 0;
        let busqueda = '';
        let filtroFecha = '';

        // Cargar selects
        function cargarSelects() {
            $.ajax({
                url: '../api/historial.php',
                method: 'GET',
                data: {
                    action: 'selects'
                },
                success: function(res) {
                    if (res.success) {
                        res.pacientes.forEach(p => {
                            $('#historial-paciente').append(`<option value="${p.id}">${p.nombre} ${p.apellido} — ${p.rut}</option>`);
                        });
                        res.medicos.forEach(m => {
                            $('#historial-medico').append(`<option value="${m.id}">Dr. ${m.nombre} ${m.apellido}</option>`);
                        });
                    }
                }
            });
        }

        // Cargar historial
        function cargarHistorial() {
            $('#tabla-historial').html(`
            <tr><td colspan="6" class="py-8 text-center text-gray-400">
                <i class="text-2xl fa-solid fa-spinner fa-spin"></i>
                <p class="mt-2">Cargando historial...</p>
            </td></tr>
        `);

            $.ajax({
                url: '../api/historial.php',
                method: 'GET',
                data: {
                    pagina: paginaActual,
                    por_pagina: porPagina,
                    busqueda,
                    fecha: filtroFecha
                },
                success: function(res) {
                    if (res.success) {
                        totalRegistros = res.total;
                        renderTabla(res.data);
                        renderPaginacion();
                    }
                }
            });
        }

        // Renderizar tabla
        function renderTabla(registros) {
            if (registros.length === 0) {
                $('#tabla-historial').html(`
                <tr><td colspan="6" class="py-8 text-center text-gray-400">
                    <i class="mb-2 text-3xl fa-solid fa-file-medical"></i>
                    <p>No se encontraron registros</p>
                </td></tr>
            `);
                return;
            }

            let html = '';
            registros.forEach(r => {
                html += `
                <tr class="transition border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-800">${r.paciente}</td>
                    <td class="px-6 py-4 text-gray-600">Dr. ${r.medico}</td>
                    <td class="max-w-xs px-6 py-4 text-gray-600 truncate">${r.diagnostico}</td>
                    <td class="max-w-xs px-6 py-4 text-gray-600 truncate">${r.tratamiento || '—'}</td>
                    <td class="px-6 py-4 text-gray-600">${r.fecha}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="editarHistorial('${r.id}')" class="text-blue-600 transition hover:text-blue-800" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button onclick="eliminarHistorial('${r.id}')" class="text-red-500 transition hover:text-red-700" title="Eliminar">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            });
            $('#tabla-historial').html(html);
        }

        // Paginación
        function renderPaginacion() {
            const totalPaginas = Math.ceil(totalRegistros / porPagina);
            const inicio = totalRegistros > 0 ? (paginaActual - 1) * porPagina + 1 : 0;
            const fin = Math.min(paginaActual * porPagina, totalRegistros);
            $('#info-paginacion').text(totalRegistros > 0 ? `Mostrando ${inicio}-${fin} de ${totalRegistros} registros` : 'Sin resultados');
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
                cargarHistorial();
            }, 400);
        });
        $('#filtro-fecha').change(function() {
            filtroFecha = $(this).val();
            paginaActual = 1;
            cargarHistorial();
        });

        // Abrir modal
        $('#btn-nuevo-historial').click(function() {
            limpiarModal();
            // Fecha de hoy por defecto
            const hoy = new Date().toISOString().split('T')[0];
            $('#historial-fecha').val(hoy);
            $('#modal-titulo').text('Nuevo Registro');
            $('#modal-historial').removeClass('hidden');
        });

        // Cerrar modal
        $('#btn-cerrar-modal, #btn-cancelar').click(function() {
            $('#modal-historial').addClass('hidden');
        });

        // Guardar
        $('#btn-guardar').click(function() {
            const id = $('#historial-id').val();
            const datos = {
                paciente_id: $('#historial-paciente').val(),
                medico_id: $('#historial-medico').val(),
                fecha: $('#historial-fecha').val(),
                sintomas: $('#historial-sintomas').val().trim(),
                diagnostico: $('#historial-diagnostico').val().trim(),
                tratamiento: $('#historial-tratamiento').val().trim(),
                observaciones: $('#historial-observaciones').val().trim()
            };

            if (!datos.paciente_id || !datos.medico_id || !datos.fecha || !datos.diagnostico) {
                $('#modal-alerta').text('Por favor completa los campos obligatorios (*)').removeClass('hidden');
                return;
            }

            if (id) datos.id = id;

            $('#btn-guardar').html('<i class="fa-solid fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);

            $.ajax({
                url: '../api/historial.php',
                method: id ? 'PUT' : 'POST',
                contentType: 'application/json',
                data: JSON.stringify(datos),
                success: function(res) {
                    if (res.success) {
                        $('#modal-historial').addClass('hidden');
                        cargarHistorial();
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
            $('#historial-id').val('');
            $('#historial-paciente, #historial-medico').val('');
            $('#historial-fecha, #historial-sintomas, #historial-diagnostico, #historial-tratamiento, #historial-observaciones').val('');
            $('#modal-alerta').addClass('hidden');
        }

        cargarSelects();
        cargarHistorial();
    });

    function editarHistorial(id) {
        $.ajax({
            url: '../api/historial.php',
            method: 'GET',
            data: {
                id: id
            },
            success: function(res) {
                if (res.success) {
                    const h = res.data;
                    $('#historial-id').val(h.id);
                    $('#historial-paciente').val(h.paciente_id);
                    $('#historial-medico').val(h.medico_id);
                    $('#historial-fecha').val(h.fecha);
                    $('#historial-sintomas').val(h.sintomas);
                    $('#historial-diagnostico').val(h.diagnostico);
                    $('#historial-tratamiento').val(h.tratamiento);
                    $('#historial-observaciones').val(h.observaciones);
                    $('#modal-titulo').text('Editar Registro');
                    $('#modal-historial').removeClass('hidden');
                }
            }
        });
    }

    function eliminarHistorial(id) {
        if (!confirm('¿Estás segura de eliminar este registro?')) return;
        $.ajax({
            url: '../api/historial.php',
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
        cargarHistorial();
    }
</script>

<?php require_once '../includes/footer.php'; ?>