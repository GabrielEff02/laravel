@extends('layouts.main')

<style>
    .card {}

    .form-control:focus {
        background-color: #E0FFFF !important;
    }

    #preview {
        margin-top: 15px;
        max-height: 150px;
        border-radius: 8px;
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


                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Barang Baru</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/brg">Barang</a></li>
                        <li class="breadcrumb-item active">Add</li>
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
                            <!-- <form action="/brg/store" id="entri" method="POST"> -->
                            <form action="/brg/store" id="entri" method="POST" enctype="multipart/form-data">

                                @csrf
                                {{-- <ul class="nav nav-tabs">
                            <li class="nav-item active">
                                <a class="nav-link active" href="#data" data-toggle="tab">Data</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#dokumen" data-toggle="tab">Dokumen</a>
                            </li>
                        </ul> --}}

                                <!-- <div class="tab-content mt-3">
                                    <div class="form-group row">
                                        <div class="col-md-2">
                                            <label for="brg_id" class="form-label">ID</label>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control brg_id" id="brg_id" name="brg_id"
                                                placeholder="ID" value="" readonly>
                                        </div>

                                    </div> -->


                                <div class="form-group row">
                                    <div class="col-md-2">
                                        <label for="brg_name" class="form-label">Nama Barang</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control brg_name" required id="brg_name"
                                            name="brg_name" placeholder="Masukkan Nama Barang">
                                    </div>
                                </div>



                                <div class="form-group row">
                                    <div class="col-md-2">
                                        <label for="price" class="form-label">Harga Rp.</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control price" id="price" name="price"
                                            placeholder="Masukkan Harga" required oninput="formatPrice(this)"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <script>
                                    function formatPrice(input) {
                                        // Hapus semua kecuali angka
                                        let value = input.value.replace(/\D/g, '');

                                        // Format angka dengan titik ribuan
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
                                            <option value="{{ $category->category_id }}">
                                                {{ $category->category_name }}
                                            </option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                <div class="form-group row mt-2">
                                    <div class="col-md-2">
                                        <label for="per" class="form-label" required>Satuan</label>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="per" class="form-control" required>
                                            <option value="">-- Pilih Satuan --</option>


                                            <option value="buah">Buah</option>
                                            <option value="ons">Ons</option>
                                            <option value="kg">KG</option>
                                            <option value="ikat">Ikat</option>
                                            <option value="pack">Pack</option>
                                            <option value="pcs">Pcs</option>
                                            <option value="box">Box</option>
                                            <option value="roll">Roll</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">

                                    <div class="col-md-2">
                                        <label for="brg_deskripsi" class="form-label">Deskripsi Barang</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control brg_deskripsi" id="brg_deskripsi"
                                            name="brg_deskripsi" placeholder="Masukkan Deskripsi Barang">
                                    </div>
                                </div>
                                <div class="form-group row">

                                    <div class="col-md-2">
                                        <label for="imageUpload" class="form-label">Gambar Produk</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="file" name="imageUpload" class="form-control imageUpload"
                                            id='imageUpload' accept=".jpeg, .jpg, .png">

                                    </div>
                                </div>
                                <div class="form-group row">

                                    <div class="col-md-2">
                                    </div>
                                    <div class="col-md-4">
                                        <img id="preview" src="#" alt="Preview" style="display: none;" />
                                    </div>
                                </div>

                                <hr style="margin-top: 30px; margin-buttom: 30px">

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

                                    var idrow = 1;
                                    var baris = 1;

                                    function tambah() {
                                        var x = document.getElementById('datatable').insertRow(baris + 1);
                                        var td1 = x.insertCell(0);
                                        var td2 = x.insertCell(1);
                                        var td3 = x.insertCell(2);
                                        var td4 = x.insertCell(3);

                                        td1.innerHTML =
                                            `<input name='REC[]' id=REC${idrow} type='text' class='REC form-control'
                                    onkeypress='return tabE(this,event)' readonly>`;
                                        td2.innerHTML =
                                            `<select name="compan_code[]" id="compan_code${idrow}" required
                                    class="form-control compan_code">
                                    <option value="">-- Pilih Cabang --</option>
                                    @foreach($listCabang as $cabang)
                                    <option value="{{ $cabang->compan_code }}">
                                        {{ $cabang->name }}
                                    </option>
                                    @endforeach
                                </select>`;
                                        td3.innerHTML = `<input name='jumlah[]' id='jumlah${idrow}' type='number'
                                    style='text-align: right' oninput='formatPrice(this)' required
                                    class='form-control jumlah text-primary'>`;
                                        td4.innerHTML = `<button type="button"
                                    class="btn btn-sm btn-circle btn-outline-danger btn-delete"
                                    onclick="hapusBaris(this)">
                                    <i class="fa fa-fw fa-trash"></i>
                                </button>`;

                                        idrow++;
                                        baris++;
                                        nomor();
                                    }

                                    function hapusBaris(btn) {
                                        var row = btn.closest('tr');
                                        row.remove();
                                        idrow--;
                                        baris--;
                                    }

                                    function nomor() {
                                        var i = 1;
                                        document.querySelectorAll(".REC").forEach(function(input) {
                                            input.value = i++;
                                        });
                                    }
                                </script>
                                <table id="datatable" class="table table-striped table-border">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="50px">No.</th>
                                            <th width="900px"><label class="form-label">Cabang</label>
                                            </th>
                                            <th width="100px">Jumlah Stok</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input name="REC[]" id="REC0" type="text" value="1"
                                                    class="form-control REC" readonly>
                                            </td>
                                            <td>
                                                <select name="compan_code[]" id="compan_code0" required
                                                    class="form-control compan_code">
                                                    <option value="">-- Pilih Cabang --</option>
                                                    @foreach($listCabang as $cabang)
                                                    <option value="{{ $cabang->compan_code }}">
                                                        {{ $cabang->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input name="jumlah[]" id="jumlah0" type="number"
                                                    style="text-align: right" oninput="formatPrice(this)" required
                                                    class="form-control jumlah text-primary">
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-sm btn-circle btn-outline-danger btn-delete"
                                                    onclick="hapusBaris(this)">
                                                    <i class="fa fa-fw fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>


                                <div class="col-md-2 row">
                                    <button type="button" onclick="tambah()" class="btn btn-sm btn-success"><i
                                            class="fas fa-plus fa-sm md-3"></i> </button>
                                </div>
                                <div class="form-group row mt-3">
                                    <div class="col-md-6">

                                        <button type="submit" class="btn btn-success btn-lg"
                                            onclick='return confirm("Apakah anda yakin Menambahkan Produk?")'>
                                            <i class="fas fa-save me-2"></i> Simpan
                                        </button>

                                    </div>
                                </div>
                        </div>


                        {{-- <button type="button"  class="btn btn-success"><i
                                            class="fa fa-save"></i> Save</button> --}}

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

@section('footer-scripts')
<!-- TAMBAH 1 -->

<script src="{{ asset('js/autoNumerics/autoNumeric.min.js') }}"></script>
<!--       <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="{{asset('foxie_js_css/bootstrap.bundle.min.js')}}"></script>

<script>
    $(document).ready(function() {

        $('body').on('click', '.btn-delete', function() {
            var val = $(this).parents("tr").remove();
            baris--;
            nomor();
        });





    });







    function simpan() {





    }
</script>
@endsection