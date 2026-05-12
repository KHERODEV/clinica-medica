<?php require_once '../includes/header.php'; ?>

<?php if ($_SESSION['usuario_rol'] !== 'admin'): ?>
    <div class="p-6 text-red-700 border border-red-200 bg-red-50 rounded-xl">
        <i class="mr-2 fa-solid fa-lock"></i> No tienes permisos para acceder a esta sección.
    </div>
<?php else: ?>

    <div class="space-y-6">

        <!-- Título -->
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-800">Usuarios</h3>
                <p class="mt-1 text-sm text-gray-500">Gestión de médicos y personal</p>
            </div>
            <button id="btn-nuevo-usuario" class="flex items-center gap-2 px-4 py-2 text-white transition bg-blue-700 rounded-lg shadow-sm hover:bg-blue-800">
                <i class="fa-solid fa-plus"></i>
                <span>Nuevo Usuario</span>
            </button>
        </div>

        <!-- Buscador -->
        <div class="grid grid-cols-1 gap-4 p-4 bg-white shadow-sm rounded-xl md:grid-cols-3">
            <div class="relative md:col-span-2">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa-solid fa-search"></i>
                </span>
                <input type="text" id="buscador" placeholder="Buscar por nombre o email..."
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
            </div>
            <select id="filtro-rol" class="border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <option value="">Todos los roles</option>
                <option value="admin">Administrador</option>
                <option value="medico">Médico</option>
                <option value="enfermera">Enfermera</option>
            </select>
        </div>

        <!-- Tabla -->
        <div class="overflow-hidden bg-white shadow-sm rounded-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 font-semibold text-left text-gray-600">Usuario</th>
                            <th class="px-6 py-3 font-semibold text-left text-gray-600">Email</th>
                            <th class="px-6 py-3 font-semibold text-left text-gray-600">Rol</th>
                            <th class="px-6 py-3 font-semibold text-left text-gray-600">Estado</th>
                            <th class="px-6 py-3 font-semibold text-center text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-usuarios">
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">
                                <i class="text-2xl fa-solid fa-spinner fa-spin"></i>
                                <p class="mt-2">Cargando usuarios...</p>
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

    <!-- Modal Nuevo/Editar Usuario -->
    <div id="modal-usuario" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black bg-opacity-50">
        <div class="w-full max-w-lg max-h-screen overflow-y-auto bg-white shadow-2xl rounded-2xl">

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h4 class="text-lg font-bold text-gray-800" id="modal-titulo">Nuevo Usuario</h4>
                <button id="btn-cerrar-modal" class="text-gray-400 transition hover:text-gray-600">
                    <i class="text-xl fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="px-6 py-4 space-y-4">
                <input type="hidden" id="usuario-id" />

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Nombre *</label>
                        <input type="text" id="usuario-nombre" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Nombre" />
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Apellido *</label>
                        <input type="text" id="usuario-apellido" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Apellido" />
                    </div>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" id="usuario-email" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="correo@clinica.com" />
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Rol *</label>
                    <select id="usuario-rol" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">Seleccionar rol...</option>
                        <option value="admin">Administrador</option>
                        <option value="medico">Médico</option>
                        <option value="enfermera">Enfermera</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700" id="label-password">Contraseña *</label>
                    <div class="relative">
                        <input type="password" id="usuario-password" class="w-full border border-gray-300 rounded-lg px-3 pr-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Mínimo 8 caracteres" />
                        <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-400" id="hint-password"></p>
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

<?php endif; ?>

<script>
    $(document).ready(function() {

        $('#page-title').text('Usuarios');

        let paginaActual = 1;
        const porPagina = 10;
        let totalUsuarios = 0;
        let busqueda = '';
        let filtroRol = '';

        // Cargar usuarios
        function cargarUsuarios() {
            $('#tabla-usuarios').html(`
            <tr><td colspan="5" class="py-8 text-center text-gray-400">
                <i class="text-2xl fa-solid fa-spinner fa-spin"></i>
                <p class="mt-2">Cargando usuarios...</p>
            </td></tr>
        `);

            $.ajax({
                url: '../api/usuarios.php',
                method: 'GET',
                data: {
                    pagina: paginaActual,
                    por_pagina: porPagina,
                    busqueda,
                    rol: filtroRol
                },
                success: function(res) {
                    if (res.success) {
                        totalUsuarios = res.total;
                        renderTabla(res.data);
                        renderPaginacion();
                    }
                }
            });
        }

        // Renderizar tabla
        function renderTabla(usuarios) {
            if (usuarios.length === 0) {
                $('#tabla-usuarios').html(`
                <tr><td colspan="5" class="py-8 text-center text-gray-400">
                    <i class="mb-2 text-3xl fa-solid fa-users"></i>
                    <p>No se encontraron usuarios</p>
                </td></tr>
            `);
                return;
            }

            const rolColor = {
                'admin': 'bg-purple-100 text-purple-700',
                'medico': 'bg-blue-100 text-blue-700',
                'enfermera': 'bg-cyan-100 text-cyan-700'
            };
            const rolLabel = {
                'admin': 'Administrador',
                'medico': 'Médico',
                'enfermera': 'Enfermera'
            };

            let html = '';
            usuarios.forEach(u => {
                html += `
                <tr class="transition border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full">
                                <i class="text-xs text-blue-600 fa-solid fa-user"></i>
                            </div>
                            <span class="font-medium text-gray-800">${u.nombre} ${u.apellido}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">${u.email}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium ${rolColor[u.rol] || 'bg-gray-100 text-gray-600'}">
                            ${rolLabel[u.rol] || u.rol}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium ${u.activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                            ${u.activo ? 'Activo' : 'Inactivo'}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="editarUsuario('${u.id}')" class="text-blue-600 transition hover:text-blue-800" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button onclick="eliminarUsuario('${u.id}', '${u.nombre} ${u.apellido}')" class="text-red-500 transition hover:text-red-700" title="Eliminar">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            });
            $('#tabla-usuarios').html(html);
        }

        // Paginación
        function renderPaginacion() {
            const totalPaginas = Math.ceil(totalUsuarios / porPagina);
            const inicio = totalUsuarios > 0 ? (paginaActual - 1) * porPagina + 1 : 0;
            const fin = Math.min(paginaActual * porPagina, totalUsuarios);
            $('#info-paginacion').text(totalUsuarios > 0 ? `Mostrando ${inicio}-${fin} de ${totalUsuarios} usuarios` : 'Sin resultados');
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
                cargarUsuarios();
            }, 400);
        });
        $('#filtro-rol').change(function() {
            filtroRol = $(this).val();
            paginaActual = 1;
            cargarUsuarios();
        });

        // Toggle password
        $('#toggle-password').click(function() {
            const input = $('#usuario-password');
            const icon = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Abrir modal nuevo
        $('#btn-nuevo-usuario').click(function() {
            limpiarModal();
            $('#label-password').text('Contraseña *');
            $('#hint-password').text('');
            $('#usuario-password').attr('placeholder', 'Mínimo 8 caracteres');
            $('#modal-titulo').text('Nuevo Usuario');
            $('#modal-usuario').removeClass('hidden');
        });

        // Cerrar modal
        $('#btn-cerrar-modal, #btn-cancelar').click(function() {
            $('#modal-usuario').addClass('hidden');
        });

        // Guardar usuario
        $('#btn-guardar').click(function() {
            const id = $('#usuario-id').val();
            const datos = {
                nombre: $('#usuario-nombre').val().trim(),
                apellido: $('#usuario-apellido').val().trim(),
                email: $('#usuario-email').val().trim(),
                rol: $('#usuario-rol').val(),
                password: $('#usuario-password').val()
            };

            if (!datos.nombre || !datos.apellido || !datos.email || !datos.rol) {
                $('#modal-alerta').text('Por favor completa los campos obligatorios (*)').removeClass('hidden');
                return;
            }

            if (!id && !datos.password) {
                $('#modal-alerta').text('La contraseña es obligatoria para nuevos usuarios').removeClass('hidden');
                return;
            }

            if (datos.password && datos.password.length < 8) {
                $('#modal-alerta').text('La contraseña debe tener mínimo 8 caracteres').removeClass('hidden');
                return;
            }

            if (id) datos.id = id;

            $('#btn-guardar').html('<i class="fa-solid fa-spinner fa-spin"></i> Guardando...').prop('disabled', true);

            $.ajax({
                url: '../api/usuarios.php',
                method: id ? 'PUT' : 'POST',
                contentType: 'application/json',
                data: JSON.stringify(datos),
                success: function(res) {
                    if (res.success) {
                        $('#modal-usuario').addClass('hidden');
                        cargarUsuarios();
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
            $('#usuario-id, #usuario-nombre, #usuario-apellido, #usuario-email, #usuario-password').val('');
            $('#usuario-rol').val('');
            $('#modal-alerta').addClass('hidden');
        }

        cargarUsuarios();
    });

    function editarUsuario(id) {
        $.ajax({
            url: '../api/usuarios.php',
            method: 'GET',
            data: {
                id: id
            },
            success: function(res) {
                if (res.success) {
                    const u = res.data;
                    $('#usuario-id').val(u.id);
                    $('#usuario-nombre').val(u.nombre);
                    $('#usuario-apellido').val(u.apellido);
                    $('#usuario-email').val(u.email);
                    $('#usuario-rol').val(u.rol);
                    $('#usuario-password').val('');
                    $('#label-password').text('Nueva Contraseña (dejar vacío para no cambiar)');
                    $('#hint-password').text('Solo completa este campo si deseas cambiar la contraseña');
                    $('#usuario-password').attr('placeholder', 'Dejar vacío para mantener la actual');
                    $('#modal-titulo').text('Editar Usuario');
                    $('#modal-usuario').removeClass('hidden');
                }
            }
        });
    }

    function eliminarUsuario(id, nombre) {
        if (!confirm(`¿Estás segura de eliminar al usuario ${nombre}?`)) return;
        $.ajax({
            url: '../api/usuarios.php',
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
        cargarUsuarios();
    }
</script>

<?php require_once '../includes/footer.php'; ?>