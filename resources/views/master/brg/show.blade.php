@extends('layouts.main')

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
                <div class="col-sm-6">
                    <h1 class="m-0">Stok Produk {{ $header->nama}}</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('master/brg')}}">List Barang</a></li>
                        <li class="breadcrumb-item active">{{$header->nama}}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <form action="{{ route('master.brg.show.storeBrgd') }}" method="POST">

                                @csrf
                                <div class="tab-content mt-3">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <div class="row">
                                                <input type="hidden" name="brg_id" value="{{ $header->brg_id }}">

                                                <!-- Gambar Produk -->
                                                <div class="col-md-3 text-center">
                                                    <img src="{{ asset('img/gambar_produk/' . $header->url) }}"
                                                        alt="{{ $header->nama }}" class="img-fluid rounded"
                                                        style="max-height: 200px;">
                                                </div>

                                                <!-- Informasi Produk -->
                                                <div class="col-md-9">
                                                    <h4 class="mb-3">{{ $header->nama }}</h4>

                                                    <div class="row mb-2">
                                                        <div class="col-md-4 font-weight-bold">Kategori:</div>
                                                        <div class="col-md-8">{{ ucfirst($header->category_name) }}
                                                        </div>
                                                    </div>

                                                    <div class="row mb-2">
                                                        <div class="col-md-4 font-weight-bold">Harga:</div>
                                                        <div class="col-md-8">Rp
                                                            {{ number_format($header->harga, 0, ',', '.') }} /
                                                            {{ $header->satuan }}
                                                        </div>
                                                    </div>

                                                    <div class="row mb-2">
                                                        <div class="col-md-4 font-weight-bold">Deskripsi:</div>
                                                        <div class="col-md-8">{{ $header->deskripsi }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>




                                    <hr style="margin-top: 30px; margin-buttom: 30px">

                                    <table id="datatable" class="table table-striped table-border">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center;">No.</th>
                                                <th style="text-align: center;">Cabang</th>
                                                <th style="text-align: center;">Jumlah Stok</th>
                                            </tr>
                                        </thead>

                                        <tbody>


                                            @foreach ($company as $i => $compan)
                                            @php
                                            $found = collect($detail)->firstWhere('compan_code', $compan->compan_code);
                                            $qty = $found ? $found->quantity : 0;
                                            @endphp
                                            <tr>
                                                <td style="width: 60px;">
                                                    <input type="text" value="{{ $i + 1 }}"
                                                        class="form-control form-control-sm text-center" readonly>
                                                </td>

                                                <td>
                                                    <input name="compan[]" type="text" value="{{ $compan->name }}"
                                                        class="form-control form-control-sm" readonly>
                                                    <input type="hidden" name="compan_code[]"
                                                        value="{{ $compan->compan_code }}">
                                                </td>

                                                <td style="width: 150px;">
                                                    <input name="jumlah[]" type="number" value="{{ $qty }}"
                                                        class="form-control form-control-sm jumlah text-end">
                                                </td>

                                            </tr>
                                            @endforeach



                                        </tbody>

                                    </table>
                                    <div class="form-group row mt-3">
                                        <div class="col-md-6"></div>
                                        <button type="submit" class="custom-btn btn-lg btn-confirm-submit">
                                            <i class="fas fa-save me-2"></i> Simpan
                                        </button>
                                    </div>


                                </div>

                            </form>

                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
    @endsection