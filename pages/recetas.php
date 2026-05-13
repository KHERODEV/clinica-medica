<?php require_once '../includes/header.php'; ?>

<div class="space-y-6">

    <!-- Título -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Recetas</h3>
            <p class="mt-1 text-sm text-gray-500">Gestión de recetas médicas</p>
        </div>
        <button id="btn-nueva-receta" class="flex items-center gap-2 px-4 py-2 text-white transition bg-blue-700 rounded-lg shadow-sm hover:bg-blue-800">
            <i class="fa-solid fa-plus"></i>
            <span>Nueva Receta</span>
        </button>
    </div>

    <!-- Buscador -->
    <div class="grid grid-cols-1 gap-4 p-4 bg-white shadow-sm rounded-xl md:grid-cols-3">
        <div class="relative md:col-span-2">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid fa-search"></i>
            </span>
            <input type="text" id="buscador" placeholder="Buscar por paciente o medicamento..."
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
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Medicamento</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Dosis</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Frecuencia</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Duración</th>
                        <th class="px-6 py-3 font-semibold text-left text-gray-600">Fecha</th>
                        <th class="px-6 py-3 font-semibold text-center text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-recetas">
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-400">
                            <i class="text-2xl fa-solid fa-spinner fa-spin"></i>
                            <p class="mt-2">Cargando recetas...</p>
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

<!-- Modal Nueva/Editar Receta -->
<div id="modal-receta" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black bg-opacity-50">
    <div class="w-full max-w-2xl max-h-screen overflow-y-auto bg-white shadow-2xl rounded-2xl">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h4 class="text-lg font-bold text-gray-800" id="modal-titulo">Nueva Receta</h4>
            <button id="btn-cerrar-modal" class="text-gray-400 transition hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="px-6 py-4 space-y-4">
            <input type="hidden" id="receta-id" />

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Paciente *</label>
                    <select id="receta-paciente" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">Seleccionar paciente...</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Médico *</label>
                    <select id="receta-medico" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">Seleccionar médico...</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Medicamento *</label>
                <input type="text" id="receta-medicamento" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Nombre del medicamento" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Dosis *</label>
                    <input type="text" id="receta-dosis" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Ej: 500mg" />
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Frecuencia *</label>
                    <input type="text" id="receta-frecuencia" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Ej: Cada 8 horas" />
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Duración *</label>
                    <input type="text" id="receta-duracion" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Ej: 7 días" />
                </div>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Indicaciones</label>
                <textarea id="receta-indicaciones" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Indicaciones adicionales..."></textarea>
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

        $('#page-title').text('Recetas');

        let paginaActual = 1;
        const porPagina = 10;
        let totalRecetas = 0;
        let busqueda = '';
        let filtroFecha = '';

        // Cargar selects
        function cargarSelects() {
            $.ajax({
                url: '../api/recetas.php',
                method: 'GET',
                data: {
                    action: 'selects'
                },
                success: function(res) {
                    if (res.success) {
                        res.pacientes.forEach(p => {
                            $('#receta-paciente').append(`<option value="${p.id}">${p.nombre} ${p.apellido} — ${p.rut}</option>`);
                        });
                        res.medicos.forEach(m => {
                            $('#receta-medico').append(`<option value="${m.id}">Dr. ${m.nombre} ${m.apellido}</option>`);
                        });
                    }
                }
            });
        }

        // Cargar recetas
        function cargarRecetas() {
            $('#tabla-recetas').html(`
            <tr><td colspan="8" class="py-8 text-center text-gray-400">
                <i class="text-2xl fa-solid fa-spinner fa-spin"></i>
                <p class="mt-2">Cargando recetas...</p>
            </td></tr>
        `);

            $.ajax({
                url: '../api/recetas.php',
                method: 'GET',
                data: {
                    pagina: paginaActual,
                    por_pagina: porPagina,
                    busqueda,
                    fecha: filtroFecha
                },
                success: function(res) {
                    if (res.success) {
                        totalRecetas = res.total;
                        renderTabla(res.data);
                        renderPaginacion();
                    }
                }
            });
        }

        // Renderizar tabla
        function renderTabla(recetas) {
            if (recetas.length === 0) {
                $('#tabla-recetas').html(`
                <tr><td colspan="8" class="py-8 text-center text-gray-400">
                    <i class="mb-2 text-3xl fa-solid fa-prescription"></i>
                    <p>No se encontraron recetas</p>
                </td></tr>
            `);
                return;
            }

            let html = '';
            recetas.forEach(r => {
                html += `
                <tr class="transition border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-800">${r.paciente}</td>
                    <td class="px-6 py-4 text-gray-600">Dr. ${r.medico}</td>
                    <td class="px-6 py-4 text-gray-600">${r.medicamento}</td>
                    <td class="px-6 py-4 text-gray-600">${r.dosis}</td>
                    <td class="px-6 py-4 text-gray-600">${r.frecuencia}</td>
                    <td class="px-6 py-4 text-gray-600">${r.duracion}</td>
                    <td class="px-6 py-4 text-gray-600">${r.fecha}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="editarReceta('${r.id}')" class="text-blue-600 transition hover:text-blue-800" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button onclick="eliminarReceta('${r.id}')" class="text-red-500 transition hover:text-red-700" title="Eliminar">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            });
            $('#tabla-recetas').html(html);
        }

        // Paginación
        function renderPaginacion() {
            const totalPaginas = Math.ceil(totalRecetas / porPagina);
            const inicio = totalRecetas > 0 ? (paginaActual - 1) * porPagina + 1 : 0;
            const fin = Math.min(paginaActual * porPagina, totalRecetas);
            $('#info-paginacion').text(totalRecetas > 0 ? `Mostrando ${inicio}-${fin} de ${totalRecetas} recetas` : 'Sin resultados');
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
                cargarRecetas();
            }, 400);
        });
        $('#filtro-fecha').change(function() {
            filtroFecha = $(this).val();
            paginaActual = 1;
            cargarRecetas();
        });

        // Abrir modal
        $('#btn-nueva-receta').click(function() {
            limpiarModal();
            $('#modal-titulo').text('Nueva Receta');
            $('#modal-receta').removeClass('hidden');
        });

        // Cerrar modal
        $('#btn-cerrar-modal, #btn-cancelar').click(function() {
            $('#modal-receta').addClass('hidden');
        });

        // Guardar
        $('#btn-guardar').click(function() {
            const id = $('#receta-id').val();
            const datos = {
                paciente_id: $('#receta-paciente').val(),
                medico_id: $('#receta-medico').val(),
                medicamento: $('#receta-medicamento').val().trim(),
                dosis: $('#receta-dosis').val().trim(),
                frecuencia: $('#receta-frecuencia').val().trim(),
                duracion: $('#receta-duracion').val().trim(),
                indicaciones: $('#receta-indicaciones').val().trim()
            };

            if (!datos.paciente_id || !datos.medico_id || !datos.medicamento || !datos.dosis || !datos.frecuencia || !datos.duracion) {
                $('#modal-alerta').text('Por favor completa los campos obligatorios (*)').removeClass('hidden');
                return;
            }

            if (id) datos.id = id;

            $('#btn-guardar').html('<i class="fa-solid fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);

            $.ajax({
                url: '../api/recetas.php',
                method: id ? 'PUT' : 'POST',
                contentType: 'application/json',
                data: JSON.stringify(datos),
                success: function(res) {
                    if (res.success) {
                        $('#modal-receta').addClass('hidden');
                        cargarRecetas();
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
            $('#receta-id, #receta-medicamento, #receta-dosis, #receta-frecuencia, #receta-duracion, #receta-indicaciones').val('');
            $('#receta-paciente, #receta-medico').val('');
            $('#modal-alerta').addClass('hidden');
        }

        cargarSelects();
        cargarRecetas();
    });

    function editarReceta(id) {
        $.ajax({
            url: '../api/recetas.php',
            method: 'GET',
            data: {
                id: id
            },
            success: function(res) {
                if (res.success) {
                    const r = res.data;
                    $('#receta-id').val(r.id);
                    $('#receta-paciente').val(r.paciente_id);
                    $('#receta-medico').val(r.medico_id);
                    $('#receta-medicamento').val(r.medicamento);
                    $('#receta-dosis').val(r.dosis);
                    $('#receta-frecuencia').val(r.frecuencia);
                    $('#receta-duracion').val(r.duracion);
                    $('#receta-indicaciones').val(r.indicaciones);
                    $('#modal-titulo').text('Editar Receta');
                    $('#modal-receta').removeClass('hidden');
                }
            }
        });
    }

    function eliminarReceta(id) {
        if (!confirm('¿Estás segura de eliminar esta receta?')) return;
        $.ajax({
            url: '../api/recetas.php',
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
        cargarRecetas();
    }
</script>

<?php require_once '../includes/footer.php'; ?>