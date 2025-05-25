<style>
    .nav-item li.active {
        border-bottom: 3px solid #338ecf;
        background: #494e52;
    }

    .item {
        padding-left: 20px;
    }

    .nav-item {
        font-size: 16px;
    }
</style>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{url('/')}}" class="brand-link" style="text-align: center">
        <img src="{{url('/img/company.jpg')}}" alt="Logo Jago" class="brand-image elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Tiara Group</span>
    </a>

    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            {{-- <div class="image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="test">
        </div> --}}
            <div class="info">
                <a href="{{url('profile')}}" class="d-block">Profile</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <!-- <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
              <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-sidebar">
                  <i class="fas fa-search fa-fw"></i>
                </button>
              </div>
            </div>
          </div> -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{url('/')}}" class="nav-link {{ (Request::is('/')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Home</p>
                    </a>
                </li>



                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-briefcase icon-blue"></i>
                        <p>
                            MASTER
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{url('brg')}}" class="nav-link item">
                                <i class="nav-icon fas fa-archive icon-white "></i>
                                <p>Barang</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{url('poin')}}" class="nav-link item">
                                <i class="nav-icon fas fa-archive icon-white "></i>
                                <p>Produk Tukar Poin</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{url('driver')}}" class="nav-link item">
                                <i class="nav-icon fas fa-archive icon-white "></i>
                                <p>Driver</p>
                            </a>
                        </li>
                    </ul>
                </li>



            </ul>
        </nav>
    </div>
</aside>