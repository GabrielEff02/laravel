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
                    <h1 class="m-0">{{$data->nama}}</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('master/satuan')}}">List Satuan</a></li>
                        <li class="breadcrumb-item active">{{$data->nama}}</li>
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
    <!-- /.content -->
    @endsection