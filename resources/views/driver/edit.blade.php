@extends('layouts.main')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Edit Driver</h1>
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
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <br>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('driver/update', $driver->username) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label for="username">Username</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="username" name="username" class="form-control"
                                    value="{{ $driver->username }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label for="name">Nama Driver</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="name" name="name" class="form-control"
                                    value="{{ old('name', $driver->driver_name) }}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label for="address">Alamat</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="address" name="address" class="form-control"
                                    value="{{ old('address', $driver->address) }}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label for="phone">Nomor Telepon</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="phone" name="phone" class="form-control"
                                    value="{{ old('phone', $driver->phone) }}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label for="email">Email</label>
                            </div>
                            <div class="col-md-4">
                                <input type="email" id="email" name="email" class="form-control"
                                    value="{{ old('email', $driver->email) }}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label for="licence_number">Plat Nomor</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="licence_number" name="licence_number" class="form-control"
                                    value="{{ old('licence_number', $driver->license_number) }}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">
                                <label for="status">Status</label>
                            </div>
                            <div class="col-md-4">
                                <select id="status" name="status" class="form-control" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="1" {{ old('status', $driver->status) == '1' ? 'selected' : '' }}>
                                        Aktif</option>
                                    <option value="0" {{ old('status', $driver->status) == '0' ? 'selected' : '' }}>
                                        Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mt-4">
                            <div class="col-md-2"></div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary"
                                    onclick="return confirm('Yakin ingin memperbarui data driver?')">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </div>
                        <div class="form-group row mt-2">
                            <div class="col-md-2"></div>
                            <div class="col-md-4">
                                <a href="{{ route('driver/resetPassword', $driver->username) }}" class="btn btn-warning"
                                    onclick="return confirm('Yakin ingin mereset password driver ini ke default (drivertiara)?')">
                                    <i class="fas fa-key"></i> Reset Password
                                </a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection