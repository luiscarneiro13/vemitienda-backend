<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

<!-- Font Awesome (mantener para íconos en vistas de contenido) -->
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/bs-stepper/css/bs-stepper.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/dropzone/min/dropzone.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/css/adminlte.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/css/jquery-ui.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/css/jquery-ui.theme.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

<!-- Tailwind CSS (preflight desactivado para no conflictuar con Bootstrap) -->
<script src="https://cdn.tailwindcss.com?plugins=container-queries"></script>
<script>
    tailwind.config = {
        corePlugins: { preflight: false },
        theme: {
            extend: {
                colors: {
                    primary: '#0052cc',
                    'primary-dark': '#003d99',
                    'surface-dark': '#0f172a',
                    'surface-light': '#f8f9fb',
                    'border-light': '#e2e8f0',
                },
                fontFamily: {
                    sans: ['Hanken Grotesk', 'sans-serif'],
                },
            }
        }
    }
</script>

<style>
    body {
        font-family: 'Hanken Grotesk', sans-serif;
        background-color: #f8f9fb;
    }
    .sidebar-custom::-webkit-scrollbar { width: 4px; }
    .sidebar-custom::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    .nav-section-content { overflow: hidden; transition: max-height 0.25s ease; }
    .nav-section-content.collapsed { max-height: 0 !important; }
    .page-item.active .page-link {
        background-color: #0052cc;
        border-color: #0052cc;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #0052cc;
        border-color: #0052cc;
    }
    #toast-container .toast-success { background-color: #1B998B; }
    .alert-success { background-color: #1B998B; border-color: #1B998B; }
</style>
