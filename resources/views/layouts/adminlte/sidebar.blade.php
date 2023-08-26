<!-- Main Sidebar Container -->
<style type="text/css">
    .not-active {
        pointer-events: null;
        cursor: none;
    }

    [class*=sidebar-dark] .brand-link {
        border-bottom: 1px solid #c9c9c9;
    }
</style>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index3.html" class="brand-link">
        <img src="{{  asset('img/logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-white">Ve mi Tienda</span>
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
                <li class="nav-item">
                    <a href="{{ url('/home') }}" class="nav-link {{ Request::segment(1)=='home' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link {{ Request::segment(1)=='admin' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-mail-bulk"></i>
                        <p>
                            Administración
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}"
                                class="nav-link {{ Request::segment(2)=='users' ? 'active' : '' }}"">
                            <i class=" nav-icon fa fa-users"></i>
                                <p>
                                    Usuarios
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}"
                                class="nav-link {{ Request::segment(2)=='categories' ? 'active' : '' }}"">
                            <i class=" nav-icon fas fa-user-edit"></i>
                                <p>
                                    Categorías
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('companies.index') }}"
                                class="nav-link {{ Request::segment(2)=='companies' ? 'active' : '' }}"">
                            <i class=" nav-icon fas fa-building"></i>
                                <p>
                                    Empresas
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
