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
            <a href="{{ $backUrl }}" class="btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <br>
            <br>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{$data->PRODUCT_NAME}}</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('transaksi/request')}}">List Barang Request</a></li>
                        <li class="breadcrumb-item active">{{$data->PRODUCT_NAME}}</li>
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
                            <form action="{{ url('transaksi/request/update/'.$data->primaryKey) }}" id="entri"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <script>
                                function formatPrice(input) {
                                    // Hapus semua kecuali angka
                                    let value = input.value.replace(/\D/g, '');

                                    // Format angka dengan titik ribuan
                                    let formatted = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                                    input.value = formatted;
                                }
                                </script>
                                @foreach ($forms as $form)
                                @if($form['type'] == 'selection')
                                <div class="form-group row mt-2"
                                    {{ $form['value'] == 'compan_code' ? "id=inputData" : '' }}>
                                    <div class="col-md-2">
                                        <label for="{{ $form['value'] }}"
                                            class="form-label">{{ $form['label'] }}</label>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="{{ $form['value'] }}" id="{{ $form['value'] }}"
                                            class="form-control" required>
                                            <option value="">-- Pilih {{ $form['label'] }} --</option>
                                            @foreach($form['option'] as $option)
                                            <option value="{{ is_array($option) ? $option['value'] : $option->value }}" {{ 
                                                    $form['value'] == 'compan_code' 
                                                        ? ($data->{is_array($form) ? $form['value'] : $form->value} == (is_array($option) ? $option['value'] : $option->value) ? 'selected' : '') 
                                                        : (
                                                            (is_array($option) ? $option['value'] : $option->value) == 1 
                                                                ? ($data['status'] == 'Waiting' ? 'selected' : '') 
                                                                : (
                                                                    (is_array($option) ? $option['value'] : $option->value) == 0 
                                                                        ? ($data['status'] == 'Rejected' ? 'selected' : '') 
                                                                        : ''
                                                                )
                                                        )}}>
                                                {{ is_array($option) ? $option['label'] : $option->label }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @if( $form['value'] != 'compan_code' )
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const select = document.getElementById('accepted');
                                    const inputField = document.getElementById('inputData');
                                    const inputField1 = document.getElementById('inputData1');

                                    function toggleInputVisibility() {
                                        console.log('Element:', select);
                                        if (select.value === '1') {
                                            inputField.style.display = 'flex';
                                            inputField1.style.display = 'flex';
                                        } else {
                                            inputField.style.display = 'none';
                                            inputField1.style.display = 'none';
                                        }
                                    }

                                    select.addEventListener('change', toggleInputVisibility);
                                    toggleInputVisibility(); // cek awal saat page load
                                });
                                </script>
                                @endif
                                @elseif($form['type'] == 'string')
                                <div class="form-group row">
                                    <div class="col-md-2">
                                        <label for="{{$form['value']}}" class="form-label">{{$form['label']}}</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control  {{$form['value']}}"
                                            {{ (isset($form['readonly']) && $form['readonly'] === false) ? '' : 'readonly' }}
                                            required id="{{$form['value']}}" name="{{$form['value']}}"
                                            placeholder="Masukkan {{$form['label']}}"
                                            value="{{ $data->{is_array($form) ? $form['value'] : $form->value} }}">
                                    </div>
                                </div>
                                @elseif($form['type'] == 'number')
                                <div class="form-group row" id="inputData1">
                                    <div class="col-md-2">
                                        <label for="{{$form['value']}}" class="form-label">{{$form['label']}}</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control {{$form['value']}}" required
                                            oninput="formatPrice(this)" id="{{$form['value']}}"
                                            name="{{$form['value']}}" placeholder="Masukkan {{$form['label']}}"
                                            value="{{ $data->{is_array(value: $form) ? $form['value'] : $form->value} }}">
                                    </div>
                                    @if($form['checkbox'])
                                    <div class="col-md-1.5">
                                        <label for="{{$form['checkboxForm']['value']}}" class="form-label"
                                            style=" margin-top: 10px; ">{{$form['checkboxForm']['label']}}</label>
                                    </div>
                                    <div class="col-md-1">
                                        <input type="checkbox" style="transform: scale(2); margin-top: 15px;"
                                            id="{{$form['checkboxForm']['value']}}"
                                            name="{{$form['checkboxForm']['value']}}">
                                    </div>
                                    @endif
                                </div>
                                @elseif($form['type'] == 'image')
                                <div class="form-group row">
                                    <div class="col-md-2">
                                        <label for="{{$form['value']}}" class="form-label">{{$form['label']}}</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="file" name="{{$form['value']}}"
                                            class="form-control {{$form['value']}}" id='{{$form['value']}}'
                                            accept=".jpeg, .jpg, .png">
                                    </div>
                                </div>
                                <div class="form-group row mt-2">
                                    <div class="col-md-2">
                                        <label for="imageNow" class="form-label">Gambar Saat Ini</label>
                                    </div>
                                    <div class="col-md-4">
                                        <img src="{{ asset($form['path'].$data->{is_array(value: $form) ? $form['value'] : $form->value}) }}"
                                            id="preview" alt="Gambar Barang" style="max-width:150px; margin-top:5px;">
                                    </div>
                                </div>
                                <script>
                                const fileInput = document.getElementById("{{$form['value']}}");
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

                                @endif
                                @endforeach
                                <div class="form-group row mt-3">
                                    <div class="col-md-6"></div>
                                    <button type="submit" class="custom-btn btn-lg btn-confirm-submit">
                                        <i class="fas fa-save me-2"></i> Simpan
                                    </button>
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