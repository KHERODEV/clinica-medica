</main>

<!-- Footer -->
<footer class="px-6 py-3 bg-white border-t border-gray-200">
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">© 2026 Clínica Médica — Todos los derechos reservados</p>
        <p class="text-sm text-gray-400">Sistema de Gestión v1.0</p>
    </div>
</footer>

</div><!-- fin contenido principal -->

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {

        // Reloj en tiempo real
        function actualizarReloj() {
            const ahora = new Date();
            const horas = String(ahora.getHours()).padStart(2, '0');
            const minutos = String(ahora.getMinutes()).padStart(2, '0');
            const segundos = String(ahora.getSeconds()).padStart(2, '0');
            $('#reloj').text(`${horas}:${minutos}:${segundos}`);
        }
        actualizarReloj();
        setInterval(actualizarReloj, 1000);

        // Toggle sidebar
        $('#toggle-sidebar').click(function() {
            const sidebar = $('#sidebar');
            const contenido = $('div.ml-64');
            if (sidebar.hasClass('w-64')) {
                sidebar.removeClass('w-64').addClass('w-0 overflow-hidden');
                contenido.removeClass('ml-64').addClass('ml-0');
            } else {
                sidebar.removeClass('w-0 overflow-hidden').addClass('w-64');
                contenido.removeClass('ml-0').addClass('ml-64');
            }
        });

    });
</script>

</body>

</html>