<style>
    .nav-link.active {
        background-color: #338ecf !important;
        color: #ffffff !important;
        border-left: 4px solid #ffffff;
        font-weight: bold;
    }

    .nav-icon {
        color: #adb5bd;
    }

    .nav-link.active .nav-icon {
        color: #ffffff !important;
    }

    .item {
        padding-left: 24px;
    }

    .nav-item {
        font-size: 16px;
    }
</style>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{url('/')}}" class="brand-link text-center">
        <img src="{{url('/img/company.jpg')}}" alt="Logo Jago" class="brand-image elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Tiara Group</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="{{url('profile')}}" class="d-block">Profile</a>
            </div>
        </div>

        @php
        $masterActive = request()->is('master/brg*') || request()->is('master/poin*') ||
        request()->is('master/driver*');
        $transaksiActive = request()->is('transaksi*');
        $reportActive = request()->is('report*');
        @endphp

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{url('/')}}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Home</p>
                    </a>
                </li>

                <li class="nav-item {{ $masterActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $masterActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            MASTER
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{url('master/brg')}}"
                                class="nav-link item {{ request()->is('master/brg*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-box"></i>
                                <p>Barang</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('master/poin')}}"
                                class="nav-link item {{ request()->is('master/poin*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-gift"></i>
                                <p>Produk Tukar Poin</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('master/driver')}}"
                                class="nav-link item {{ request()->is('master/driver*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Driver</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{ $transaksiActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $transaksiActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>
                            TRANSAKSI
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('transaksi/jual') }}"
                                class="nav-link item {{ request()->is('transaksi/jual*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-shopping-cart"></i>
                                <p>Penjualan Barang</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{ $reportActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $reportActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                            REPORT
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('report/penjualan') }}"
                                class="nav-link item {{ request()->is('report/penjualan*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Penjualan</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>