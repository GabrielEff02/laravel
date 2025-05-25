@extends('layouts.main')
@section('styles')
<!-- <link rel="stylesheet" href="{{url('http://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css') }}"> -->
<link rel="stylesheet" href="{{asset('foxie_js_css/jquery.dataTables.min.css')}}" />

@endsection

<style>
    .card-body {
        padding: 5px 10px !important;
    }

    .table thead {
        background-color: #c6e2ff;
        color: #000;
    }

    .datatable tbody td {
        padding: 5px !important;
    }

    .datatable {
        border-right: solid 2px #000;
        border-left: solid 2px #000;
    }

    .table tbody:nth-child(2) {
        background-color: #ffe4e1;
    }

    .btn-secondary {
        background-color: #42047e !important;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #CFCACAFF !important;
        /* abu muda */
    }

    .table-striped tbody tr:nth-of-type(even) {
        background-color: #ffffff !important;
        /* putih */
    }

    th {
        font-size: 13px;
    }

    td {
        font-size: 13px;
    }
</style>

@section('content')
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
            </div>
            @endif
            <script>
                setTimeout(() => {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(alert => {
                        alert.classList.remove('show');
                        alert.classList.add('fade');
                        setTimeout(() => alert.remove(), 500);
                    });
                }, 3000);
            </script>
            <br>

            <div class="row mb-2">
                <div class="col-sm-12">
                    <b class="m-0" style="font-size: 14pt;">List Driver</b>
                </div>
                <!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Status -->
    @if (session('status'))
    <div class="alert alert-success">
        {{session('status')}}
    </div>
    @endif
    <script>
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            });
        }, 3000);
    </script>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <table class="table table-fixed table-striped table-border table-hover nowrap datatable"
                                id="datatable">
                                <thead class="table-dark">
                                    <tr>

                                        <th scope="col" style="text-align: center">No.</th>
                                        <th scope="col" style="text-align: center">Username</th>
                                        <th scope="col" style="text-align: center">Nama Driver</th>
                                        <th scope="col" style="text-align: center">Alamat Driver</th>
                                        <th scope="col" style="text-align: center">Nomor Telepon</th>
                                        <th scope="col" style="text-align: center">Email</th>
                                        <th scope="col" style="text-align: center">Plat Nomor</th>
                                        <th scope="col" style="text-align: center">Status</th>
                                        <th scope="col" style="text-align: center">Manager</th>
                                        <th scope="col" style="text-align: center">Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection

@section('javascripts')


<script>
    $(document).ready(function() {
        var dataTable = $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            autoWidth: true,
            order: [
                [0, "asc"]
            ],
            ajax: {
                url: "{{ route('get-driver') }}"
            },
            columns: [{
                    data: 'driver_id',
                    name: 'driver_id'
                },
                {
                    data: 'username',
                    name: 'username'
                }, {
                    data: 'driver_name',
                    name: 'driver_name'
                },
                {
                    data: 'address',
                    name: 'address'
                },
                {
                    data: 'phone',
                    name: 'phone',
                },

                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'license_number',
                    name: 'license_number'
                }, {
                    data: 'status',
                    name: 'status'
                }, {
                    data: 'manager',
                    name: 'manager'
                }, {
                    data: 'action',
                    name: 'action'
                }

            ],
            columnDefs: [{
                className: "dt-center",
                targets: [0, 6, 8]
            }],
            lengthMenu: [

            ],
            dom: "<'row'<'col-md-6'><'col-md-6'>>" +
                "<'row'<'col-md-2'l><'col-md-6 test_btn m-auto'><'col-md-4'f>>" +
                "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
            stateSave: true
        });

        $("div.test_btn").html(
            `<a class="btn btn-lg btn-md btn-success" href="{{ url('driver/create') }}">
            <i class="fas fa-plus fa-sm md-3"></i>
        </a>`
        );
        // $('.datatable tbody').on('click', 'tr', function() {
        //   var data = dataTable.row(this).data();
        //   if (data && data.brg_id) {
        //     window.location.href = "/brg/show/" + data.brg_id;
        //   }
        // });


    });
</script>
@endsection