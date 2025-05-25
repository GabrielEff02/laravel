@extends('layouts.main')


<style>
    .card {}

    .form-control:focus {
        background-color: #E0FFFF !important;
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
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Barang {{$brg->brg_name}}</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('/brg')}}">List Barang</a></li>
                        <li class="breadcrumb-item active">Edit {{$brg->brg_name}}</li>
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
                            <form action="{{ url('/brg/update/'.$brg->brg_id) }}" id="entri" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-md-2">
                                        <label for="brg_name" class="form-label">Nama Barang</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control brg_name" required id="brg_name"
                                            name="brg_name" placeholder="Masukkan Nama Barang"
                                            value="{{ $brg->brg_name }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-2">
                                        <label for="price" class="form-label">Harga Rp.</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control price" id="price" name="price"
                                            placeholder="Masukkan Harga" required oninput="formatPrice(this)"
                                            value="{{ $brg->price }}" autocomplete="off">
                                    </div>
                                </div>

                                <script>
                                    function formatPrice(input) {
                                        let value = input.value.replace(/\D/g, '');
                                        let formatted = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                        input.value = formatted;
                                    }
                                </script>

                                <div class="form-group row mt-2">
                                    <div class="col-md-2">
                                        <label for="category_id" class="form-label">Kategori</label>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="category_id" class="form-control" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach($categories as $category)
                                            <option value="{{ $category->category_id }}" @if ($category->category_id ==
                                                $brg->category_id) selected @endif>
                                                {{ $category->category_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row mt-2">
                                    <div class="col-md-2">
                                        <label for="per" class="form-label">Satuan</label>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="per" class="form-control" required>
                                            <option value="">-- Pilih Satuan --</option>
                                            <option value="buah" {{ $brg->per == 'buah' ? 'selected' : '' }}>Buah
                                            </option>
                                            <option value="ons" {{ $brg->per == 'ons' ? 'selected' : '' }}>Ons</option>
                                            <option value="kg" {{ $brg->per == 'kg' ? 'selected' : '' }}>KG</option>
                                            <option value="ikat" {{ $brg->per == 'ikat' ? 'selected' : '' }}>Ikat
                                            </option>
                                            <option value="pack" {{ $brg->per == 'pack' ? 'selected' : '' }}>Pack
                                            </option>
                                            <option value="pcs" {{ $brg->per == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                            <option value="box" {{ $brg->per == 'box' ? 'selected' : '' }}>Box</option>
                                            <option value="roll" {{ $brg->per == 'roll' ? 'selected' : '' }}>Roll
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-2">
                                        <label for="brg_deskripsi" class="form-label">Deskripsi Barang</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control brg_deskripsi" id="brg_deskripsi"
                                            name="brg_deskripsi" value="{{ $brg->brg_deskripsi }}"
                                            placeholder="Masukkan Deskripsi Barang">
                                    </div>
                                </div>

                                <!-- Tambahan input untuk upload gambar -->
                                <div class="form-group row mt-2">
                                    <div class="col-md-2">
                                        <label for="imageUpload" class="form-label">Gambar Barang</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="file" class="form-control" id="imageUpload" name="imageUpload"
                                            accept="image/*">

                                    </div>
                                </div>

                                <div class="form-group row mt-2">
                                    <div class="col-md-2">
                                        <label for="imageNow" class="form-label">Gambar Saat Ini</label>
                                    </div>
                                    <div class="col-md-4">
                                        <img src="{{ asset('img/gambar_produk/'.$brg->url) }}" id="preview"
                                            alt="Gambar Barang" style="max-width:150px; margin-top:5px;">
                                    </div>
                                </div>
                                <script>
                                    const fileInput = document.getElementById('imageUpload');
                                    const previewImage = document.getElementById('preview');

                                    fileInput.addEventListener('change', function() {
                                        const file = this.files[0];
                                        if (file) {

                                            // Tampilkan gambar
                                            const reader = new FileReader();
                                            reader.onload = function(e) {
                                                previewImage.src = e.target.result;
                                                previewImage.style.display = 'block';
                                            };
                                            reader.readAsDataURL(file);
                                        } else {
                                            fileLabel.textContent = 'Upload Foto';
                                            previewImage.style.display = 'none';
                                            previewImage.src = '#';
                                        }
                                    });
                                </script>
                                <div class="form-group row mt-3">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-success btn-lg"
                                            onclick='return confirm("Apakah anda yakin Mengubah Detail Barang?")'>

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