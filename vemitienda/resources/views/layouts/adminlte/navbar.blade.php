@php
    $sections = [
        'usuarios'       => ['Usuarios',           'Panel de administración de cuentas'],
        'blog'           => ['Blog',                'Gestiona los artículos del blog'],
        'postcategory'   => ['Categorías de blog',  'Organiza el contenido por categorías'],
        'tags'           => ['Etiquetas',            'Administra las etiquetas de los artículos'],
        'plans'          => ['Planes',               'Gestiona los planes de suscripción'],
        'versions'       => ['Versiones Android',    'Control de versiones de la app móvil'],
        'planusers'      => ['Plan usuarios',        'Asignación de planes a usuarios'],
        'paymentmethods' => ['Métodos de pago',      'Configura los métodos de pago disponibles'],
        'payments'       => ['Pagos',                'Historial y gestión de pagos'],
    ];
    $seg       = Request::segment(2);
    $pageTitle = $sections[$seg][0] ?? 'Panel';
    $pageSub   = $sections[$seg][1] ?? 'Ve mi tienda';
@endphp

<header class="h-16 flex items-center justify-between px-8 bg-white border-b border-slate-200 sticky top-0 z-10">

    <div>
        <div class="flex items-baseline gap-2">
            <h2 class="text-sm font-bold text-slate-900 leading-tight">{{ $pageTitle }}</h2>
            <span class="text-slate-300 text-xs">·</span>
            <p class="text-[11px] text-slate-500">{{ $pageSub }}</p>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <div class="flex items-center gap-2 text-sm text-slate-600">
            <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white">
                {{ strtoupper(substr(Auth::user()->name ?? Auth::user()->email, 0, 1)) }}
            </div>
            <span class="font-medium">{{ Auth::user()->name ?? Auth::user()->email }}</span>
        </div>
    </div>

</header>
