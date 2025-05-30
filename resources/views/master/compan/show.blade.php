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
                    <h1 class="m-0">{{$data->name}}</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('master/compan')}}">List Perusahaan</a></li>
                        <li class="breadcrumb-item active">{{$data->name}}</li>
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
                            @foreach ($forms as $form)
                            @if($form['type'] == 'selection')
                            <div class="form-group row mt-2">
                                <div class="col-md-2">
                                    <label for="{{ $form['value'] }}" class="form-label">{{ $form['label'] }}</label>
                                </div>
                                <div class="col-md-4">
                                    <select name="{{ $form['value'] }}" class="form-control" readonly>
                                        <option value="">-- Pilih {{ $form['label'] }} --</option>
                                        @foreach($form['option'] as $option)
                                        <option value="{{ is_array($option) ? $option['value'] : $option->value }}"
                                            {{ $data->{is_array($form) ? $form['value'] : $form->value} == (is_array($option) ? $option['value'] : $option->value) ? 'selected' : '' }}>
                                            {{ is_array($option) ? $option['label'] : $option->label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @elseif($form['type'] == 'string')
                            <div class="form-group row">
                                <div class="col-md-2">
                                    <label for="{{$form['value']}}" class="form-label">{{$form['label']}}</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control {{$form['value']}}" readonly
                                        id="{{$form['value']}}" name="{{$form['value']}}"
                                        placeholder="Masukkan {{$form['label']}}"
                                        value="{{ $data->{is_array($form) ? $form['value'] : $form->value} }}">
                                </div>
                            </div>
                            @elseif($form['type'] == 'number')
                            <div class="form-group row">
                                <div class="col-md-2">
                                    <label for="{{$form['value']}}" class="form-label">{{$form['label']}}</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control {{$form['value']}}" readonly
                                        oninput="formatPrice(this)" id="{{$form['value']}}" name="{{$form['value']}}"
                                        placeholder="Masukkan {{$form['label']}}"
                                        value="{{ $data->{is_array(value: $form) ? $form['value'] : $form->value} }}">
                                </div>
                            </div>
                            @elseif($form['type'] == 'image')
                            <div class="form-group row">
                                <div class="col-md-2">
                                    <label for="{{$form['value']}}" class="form-label">{{$form['label']}}</label>
                                </div>

                                <div class="col-md-4">
                                    <img src="{{ asset($form['path'].$data->{is_array(value: $form) ? $form['value'] : $form->value}) }}"
                                        id="preview" alt="Gambar Barang" style="max-width:150px; margin-top:5px;">
                                </div>
                            </div>
                            @elseif($form['type'] == 'barcode')
                            <div class="form-group row">
                                <div class="col-md-2">
                                    <label for="{{$form['value']}}" class="form-label">{{$form['label']}}</label>
                                </div>

                                <div class="col-md-4">
                                    <pre hidden id="code" class="form-label">
                                    {{ $data->{is_array(value: $form) ? $form['value'] : $form->value} }}
                                    </pre>
                                    <div id="barcode"></div>
                                </div>
                                <div class="col-md-2 position-relative">
                                    <button id="downloadBtn" class="btn custom-btn position-absolute"
                                        style="bottom: 0; left: 0; right: 0; ">
                                        Download QRCode</button>
                                </div>
                            </div>
                            @elseif($form['type'] == 'textarea')
                            <div class="form-group row">
                                <div class="col-md-2">
                                    <label for="{{$form['value']}}" class="form-label">{{$form['label']}}</label>
                                </div>

                                <div class="col-md-4">
                                    <textarea class="form-control" rows="2"
                                        readonly>{{$data->{is_array(value: $form) ? $form['value'] : $form->value}  }}</textarea>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const qrValueElement = document.getElementById("code");
        const value = qrValueElement?.textContent?.trim();

        if (!value) return;

        const qrcode = new QRCode(document.getElementById("barcode"), {
            text: value,
            width: 256,
            height: 256,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        document.getElementById('downloadBtn').addEventListener('click',
            () => {
                const originalCanvas = document.querySelector(
                    '#barcode canvas');
                if (!originalCanvas) {
                    alert('QR code belum dibuat!');
                    return;
                }

                // Ukuran canvas QR asli
                const originalSize = 512;

                // Padding yang diinginkan (dalam pixel)
                const padding = 80;

                // Canvas baru dengan ukuran lebih besar (QR size + 2 * padding)
                const newSize = originalSize + padding * 2;
                const paddedCanvas = document.createElement('canvas');
                paddedCanvas.width = newSize;
                paddedCanvas.height = newSize;
                const ctx = paddedCanvas.getContext('2d');

                // Isi background putih agar padding putih (bisa diganti warna lain)
                ctx.fillStyle = "#ffffff";
                ctx.fillRect(0, 0, newSize, newSize);

                // Gambar canvas QR asli di tengah canvas baru
                ctx.drawImage(originalCanvas, padding, padding,
                    originalSize, originalSize);

                // Konversi canvas baru ke data URL jpg
                const jpgUrl = paddedCanvas.toDataURL('image/jpeg',
                    1.0);

                // Buat link download dan trigger click
                const downloadLink = document.createElement('a');
                downloadLink.href = jpgUrl;
                downloadLink.download = 'qrcode.jpg';
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            });
    });
    </script>


    <!-- /.content -->
    @endsection