<!-- Main Sidebar Container -->
<style type="text/css">
    .not-active {
        pointer-events: null;
        cursor: none;
    }
</style>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index3.html" class="brand-link">
        <img src="{{ asset('img/logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-white">Recluta</span>
    </a>
    <div class="sidebar">
        <!-- SidebarSearch Form -->

        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="hidden" placeholder="Search" aria-label="Search">
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('payments.index') }}"
                        class="nav-link {{ Request::segment(2)=='payments' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Pagos</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link {{ Request::segment(1)=='admin' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Administración
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="{{ route('paymentmethods.index') }}"
                                class="nav-link {{ Request::segment(2)=='paymentmethods' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cog"></i>
                                <p>Métodos de Pago</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('planusers.index') }}"
                                class="nav-link {{ Request::segment(2)=='planusers' ? 'active' : '' }}">
                                <i class="nav-icon fa fa-users"></i>
                                <p>Planes de Usuarios</p>
                            </a>
                        </li>

                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link {{ Request::segment(1)=='admin' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Configuración
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="{{ route('usuarios.index') }}"
                                class="nav-link {{ Request::segment(2)=='usuarios' ? 'active' : '' }}">
                                <i class="nav-icon fa fa-user-alt"></i>
                                <p>Usuarios</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('plans.index') }}"
                                class="nav-link {{ Request::segment(2)=='plans' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book-open"></i>
                                <p>Planes</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <form style="display: inline" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <a href="#" onclick="this.closest('form').submit()" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                    </form>
                </li>

                {{-- <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>
                            Salir
                        </p>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li> --}}
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
