@extends('layouts.main')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Tambah Driver</h1>
        </div>
    </div>

    <div class="content">
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
            <!-- <script>
                setTimeout(() => {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(alert => {
                        alert.classList.remove('show');
                        alert.classList.add('fade');
                        setTimeout(() => alert.remove(), 500);
                    });
                }, 3000);
            </script> -->
            <br>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('driver/store') }}" method="POST">
                        @csrf

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label class="form-label" for="username">Username</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="username" name="username" class="form-control"
                                    placeholder="Masukkan username" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label class="form-label" for="name">Nama Driver</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="name" name="name" class="form-control"
                                    placeholder="Masukkan nama driver" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label class="form-label" for="address">Alamat</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="address" name="address" class="form-control"
                                    placeholder="Masukkan alamat driver" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label class="form-label" for="phone">Nomor Telepon</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="phone" name="phone" class="form-control"
                                    placeholder="Masukkan nomor telepon" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label class="form-label" for="email">Email</label>
                            </div>
                            <div class="col-md-4">
                                <input type="email" id="email" name="email" class="form-control"
                                    placeholder="Masukkan email driver" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label class="form-label" for="licence_number">Plat Nomor</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="licence_number" name="licence_number" class="form-control"
                                    placeholder="Contoh: L1213JKL" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label class="form-label" for="password">Password</label>
                            </div>
                            <div class="col-md-4">
                                <input type="password" id="password" name="password" class="form-control"
                                    placeholder="Masukkan password" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                            </div>
                            <div class="col-md-4">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-control" placeholder="Ulangi password" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label class="form-label" for="status">Status</label>
                            </div>
                            <div class="col-md-4">
                                <select id="status" name="status" class="form-control" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mt-4">
                            <div class="col-md-2"></div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success"
                                    onclick="return confirm('Yakin ingin menyimpan data driver?')">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection