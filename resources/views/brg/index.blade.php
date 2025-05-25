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
            <br>
            <div class="row mb-2">
                <div class="col-sm-12">
                    <b class="m-0" style="font-size: 14pt;">List Barang </b>
                </div>
                <!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Status -->

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
                                        <th scope="col" style="text-align: center">Nama Barang</th>
                                        <th scope="col" style="text-align: center">Category</th>
                                        <th scope="col" style="text-align: center">Total Produk</th>
                                        <th scope="col" style="text-align: center">Harga</th>
                                        <th scope="col" style="text-align: center">Per</th>
                                        <th scope="col" style="text-align: center">Deskripsi Barang</th>
                                        <th scope="col" style="text-align: center">Url</th>
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
            url: "{{ route('get-brg') }}"
        },
        columns: [{
                data: 'brg_id',
                name: 'brg_id'
            },
            {
                data: 'brg_name',
                name: 'brg_name'
            },
            {
                data: 'category_name',
                name: 'category_name'
            },
            {
                data: 'total_produk',
                name: 'total_produk'
            },
            {
                data: 'price',
                name: 'price',
                render: $.fn.dataTable.render.number('.', ',', 0, '')
            },
            {
                data: 'per',
                name: 'per'
            },
            {
                data: 'brg_deskripsi',
                name: 'brg_deskripsi'
            },
            {
                data: 'url',
                name: 'url'
            }, {
                data: 'action',
                name: 'action'
            }

        ],
        columnDefs: [{
                className: "dt-center",
                targets: [0]
            },
            {
                className: "dt-right",
                targets: [4]
            }
        ],
        lengthMenu: [
            [10, 25, 50, 100, 100],
            [10, 25, 50, 100]
        ],
        dom: "<'row'<'col-md-6'><'col-md-6'>>" +
            "<'row'<'col-md-2'l><'col-md-6 test_btn m-auto'><'col-md-4'f>>" +
            "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
        stateSave: true
    });

    $("div.test_btn").html(
        `<a class="btn btn-lg btn-md btn-success" href="{{ url('brg/create') }}">
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