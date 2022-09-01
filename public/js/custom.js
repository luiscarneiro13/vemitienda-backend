(function ($, DataTable) {
    // Tablas
    let language_tables = "Spanish.json";

    $('.tabla-default').DataTable({
        proccesing: true,
        serverSider: true,
        responsive: true,
        language: { "url": language_tables },
        order: [],
        drawCallback: function (settings) {
            $('ul.pagination').addClass("pagination-sm");
        }
    });

    $('.formEliminar').on('click', function (e) {
        e.preventDefault();
        var nombre = $(this).attr('data-nombre');
        Swal.fire({
            title: 'Leer detenidamente',
            text: 'Quiere eliminar a: ' + nombre + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fa fa-thumbs-up"></i> Si, seguro!',
            cancelButtonText: `Cancelar`,
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            } else {
                Swal.fire('Los Cambios no serán realizados!', '', 'info')
            }
        })
    })
    
})(jQuery, jQuery.fn.dataTable);