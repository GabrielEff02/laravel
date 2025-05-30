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
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
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
            <br>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tetapkan Driver</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('/transaksi/tukar')}}">Pentukaran Barang</a></li>
                        <li class="breadcrumb-item active">Tetapkan Driver</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- Status -->

    </script>
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card" style="padding-right:30px !important; padding-left:30px !important;">
                        <form action={{url('/transaksi/tukar/update')}} id="entri" method="POST">
                            @csrf
                            <table class="table table-fixed table-striped table-border table-hover nowrap datatable"
                                id="datatable">
                                <thead class=" table-dark">
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                            <div class="form-group row mt-3">
                                <div class="col-md-6"></div>
                                <button type="submit" class="custom-btn btn-lg btn-confirm-submit">
                                    <i class="fas fa-save me-2"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
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
    $('#datatable').on('click', '.clickable-row', function(e) {
        if (!$(e.target).is('input[type="checkbox"]')) {
            const id = $(this).data('id');
            window.location.href = `/transaksi/tukar/show/${id}`;
        }
    });

    $('#datatable').on('change', '#checkAll', function() {
        $('.row-checkbox').prop('checked', this.checked);
    });


    $(document).ready(function() {
        $.ajax({
            url: "{{ route('transaksi.get-tukar-ambil') }}",
            type: "GET",
            success: function(response) {
                let thead = '<tr>';
                response.columns.forEach(col => {
                    thead += `<th style="text-align:center">${col.title}</th>`;
                });
                thead += '</tr>';
                $('#datatable thead').html(thead);

                $('.datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    // scrollX: true,

                    autoWidth: true,
                    ajax: "{{ route('transaksi.get-tukar-ambil') }}",
                    columns: response.columns,
                    paging: false,

                    dom: "<'row'<'col-md-6'><'col-md-6'>>" +
                        "<'row'<'col-md-2'l><'col-md-6 test_btn m-auto'><'col-md-4'f>>" +
                        "<'row'<'col-md-12't>><'row'<'col-md-12'ip>>",
                    stateSave: true
                });
            }
        });
    });
</script>
@endsection