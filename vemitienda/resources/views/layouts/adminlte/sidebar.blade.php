@php
    $seg2 = Request::segment(2);
    $blogOpen   = in_array($seg2, ['blog', 'postcategory', 'tags']);
    $configOpen = in_array($seg2, ['usuarios', 'versions', 'plans', 'planusers', 'paymentmethods', 'payments']);

    $navLink = fn($route, $icon, $label, $seg) =>
        '<a href="' . route($route) . '"
            class="flex items-center gap-2 px-3 py-1.5 rounded-md transition-colors ' .
            ($seg2 === $seg
                ? 'bg-white/15 text-white font-semibold'
                : 'text-slate-400 hover:text-white hover:bg-white/10') . '"
            style="font-size:12px; text-decoration:none">
            <span class="material-symbols-outlined" style="font-size:15px">' . $icon . '</span>' . $label . '
        </a>';
@endphp

<aside class="sidebar-custom w-[220px] bg-[#0f172a] text-white flex flex-col h-screen fixed left-0 top-0 z-20 overflow-y-auto">

    {{-- Logo centrado --}}
    <div class="flex flex-col items-center pt-3 px-3 pb-2 border-b border-white/10">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="w-7 h-7 object-contain rounded-lg mb-1">
        <h1 class="text-xs font-bold tracking-tight leading-none">Ve mi tienda</h1>
        <p class="text-[8px] text-blue-400 font-semibold uppercase tracking-widest mt-0.5">Admin Panel</p>
    </div>

    {{-- Navegación --}}
    <div class="flex-1 px-2 py-2">
        <nav class="space-y-0.5">

            {{-- Crear Contenido --}}
            <a href="{{ route('blog.create') }}"
                class="flex items-center gap-2 px-3 py-1.5 rounded-md transition-colors mb-1 {{ Request::is('admin/blog/create') ? 'bg-[#0052cc] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-white/10' }}"
                style="font-size:12px; text-decoration:none">
                <span class="material-symbols-outlined" style="font-size:15px">add_circle</span>
                Crear Contenido
            </a>

            {{-- Admin Blog --}}
            <div>
                <button
                    onclick="toggleSection('blog-section', this)"
                    style="background:transparent;border:none;outline:none;width:100%"
                    class="flex items-center justify-between px-3 py-1.5 text-xs text-slate-300 hover:bg-white/10 rounded-md cursor-pointer transition-colors">
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:15px">article</span>
                        Admin Blog
                    </span>
                    <span class="material-symbols-outlined section-chevron transition-transform {{ $blogOpen ? 'rotate-180' : '' }}" style="font-size:14px">
                        expand_more
                    </span>
                </button>
                <div id="blog-section"
                    class="nav-section-content pl-3 space-y-0.5 mt-0.5 {{ $blogOpen ? '' : 'collapsed' }}"
                    style="max-height:{{ $blogOpen ? '200px' : '0' }}">
                    {!! $navLink('blog.index', 'newspaper', 'Blog', 'blog') !!}
                    {!! $navLink('postcategory.index', 'category', 'Categorías', 'postcategory') !!}
                    {!! $navLink('tags.index', 'label', 'Etiquetas', 'tags') !!}
                </div>
            </div>

            {{-- Configuración --}}
            <div>
                <button
                    onclick="toggleSection('config-section', this)"
                    style="background:transparent;border:none;outline:none;width:100%"
                    class="flex items-center justify-between px-3 py-1.5 text-xs text-slate-300 hover:bg-white/10 rounded-md cursor-pointer transition-colors">
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:15px">settings</span>
                        Configuración
                    </span>
                    <span class="material-symbols-outlined section-chevron transition-transform {{ $configOpen ? 'rotate-180' : '' }}" style="font-size:14px">
                        expand_more
                    </span>
                </button>
                <div id="config-section"
                    class="nav-section-content pl-3 space-y-0.5 mt-0.5 {{ $configOpen ? '' : 'collapsed' }}"
                    style="max-height:{{ $configOpen ? '200px' : '0' }}">
                    {!! $navLink('usuarios.index', 'person', 'Usuarios', 'usuarios') !!}
                    {!! $navLink('versions.index', 'android', 'Versiones Android', 'versions') !!}
                </div>
            </div>

        </nav>
    </div>

    {{-- Logout --}}
    <div class="px-2 py-2 border-t border-white/10">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                style="background:transparent;border:none;outline:none;width:100%"
                class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-400 hover:text-white hover:bg-white/10 rounded-md transition-colors cursor-pointer">
                <span class="material-symbols-outlined" style="font-size:15px">logout</span>
                Cerrar sesión
            </button>
        </form>
    </div>

</aside>

<script>
    function toggleSection(id, btn) {
        const panel = document.getElementById(id);
        const chevron = btn.querySelector('.section-chevron');
        const isOpen = !panel.classList.contains('collapsed');
        if (isOpen) {
            panel.style.maxHeight = '0';
            panel.classList.add('collapsed');
            chevron.classList.remove('rotate-180');
        } else {
            panel.style.maxHeight = '200px';
            panel.classList.remove('collapsed');
            chevron.classList.add('rotate-180');
        }
    }
</script>
