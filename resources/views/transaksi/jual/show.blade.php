@extends('layouts.main')
<style>
    .custom {
        background-color: #F7D8B4 !important;
        border: 1px solid #ced4da !important;
    }

    .form-control {
        font-size: 16px !important;
        font-weight: bold !important;
    }
</style>
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <br>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Transaksi #{{ $header->transaction_id }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('transaksi') }}">Transaksi</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <!-- Info Utama -->
            <div class="card mb-4">
                <div class="card-body">

                    <div class="form-group row">
                        <div class="col-md-2">
                            <label class="form-label">Nama Pemesan</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" value="{{ $header->name }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">
                            <label class="form-label">Telepon</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" value="{{ $header->phone }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">
                            <label class="form-label">Email</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" value="{{ $header->email }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">
                            <label class="form-label">Alamat Pengiriman</label>
                        </div>
                        <div class="col-md-4">
                            <textarea class="form-control" rows="2" readonly>{{ $header->address }}</textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">
                            <label class="form-label">Tanggal Transaksi</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" value="{{ $header->transaction_date }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">
                            <label class="form-label">Pengiriman</label>
                        </div>
                        <div class="col-md-4">
                            @if($header->is_delivery == 1)
                            <div class="form-control custom d-flex align-items-center">
                                <i class="fas fa-shipping-fast me-2"></i>
                            </div>
                            @else
                            <div class="form-control custom d-flex align-items-center">
                                <i class="fas fa-store me-2"></i>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label class="form-label">Cabang</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" value="{{ $header->compan_name }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">
                            <label class="form-label">Driver</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control"
                                value="{{ $header->is_delivery == 1 ? ($header->driver_name == ''?'Belum Ditetapkan':$header->driver_name) : '-' }}"
                                readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" value="@php
                                switch($header->status){
                                    case 0: echo 'Barang Belum Siap'; break;
                                    case 1: echo 'Barang Sudah Siap'; break;
                                    case 2: echo 'Barang Sedang Diantar'; break;
                                    case 3: echo 'Pesanan Selesai'; break;
                                    default: echo '-';
                                }
                            @endphp
                            " readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">
                            <label class="form-label">Total Jumlah Barang</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control"
                                value="{{ $header->total_quantity->total_quantity }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-2">
                            <label class="form-label">Total Harga</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control"
                                value="Rp {{ number_format($header->total_amount, 0, ',', '.') }}" readonly>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Detail Produk -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Rincian Barang</h5>
                </div>
                <div class="card-body table-responsive">
                    <table id="datatable" class="table table-striped table-border">
                        <thead>
                            <tr class="text-center">
                                <th style="text-align: center;">No</th>
                                <th style="text-align: center;">Nama Barang</th>
                                <th style="text-align: center;">Jumlah</th>
                                <th style="text-align: center;">Harga Satuan</th>
                                <th style="text-align: center;">Subtotal</th>
                                <th style="text-align: center;">Gambar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detail as $i => $item)
                            <tr>
                                <td style="width: 60px;">
                                    <input type="text" value="{{ $i + 1 }}"
                                        class="form-control form-control-sm text-center" style="height: 60px; "
                                        readonly>
                                </td>
                                <td>
                                    <input type="text" value="{{ $item->nama }}" class="form-control form-control-sm"
                                        style="height: 60px; " readonly>
                                </td>
                                <td>
                                    <input type="text" value="{{ $item->quantity }} {{ ucfirst($item->satuan) }}"
                                        class="form-control form-control-sm text-right" style="height: 60px; " readonly>
                                </td>
                                <td>
                                    <input type="text"
                                        value="Rp. {{ number_format($item->harga, 0, ',', '.') }}/{{ $item->satuan }}"
                                        class="form-control form-control-sm text-right" style="height: 60px; " readonly>
                                </td>
                                <td>
                                    <input type="text" value="Rp. {{ number_format($item->total_price, 0, ',', '.') }}"
                                        class="form-control form-control-sm text-right" style="height: 60px; " readonly>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <img src="{{ asset('img/gambar_produk/' . $item->url) }}" alt="{{ $item->nama }}"
                                        style="height: 60px; width: 100px; object-fit: fill;">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection