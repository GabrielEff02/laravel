@extends('layouts.main')

@section('content')
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Profile User</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item active">Profile User</li>
					</ol>
				</div>
			</div>
		</div>
	</div>

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

	<div class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">

					<div class="card">
						<div class="card-body">
							<form method="POST" action="{{ url('profile/update') }}" enctype="multipart/form-data">
								@csrf
								<div class="form-group row">
									<label class="col-md-3 control-label">Username</label>
									<div class="col-md-9">
										<input type="text" name="username" class="form-control"
											value="{{ auth()->user()->username }}">
									</div>
								</div>

								<div class="form-group row">
									<label class="col-md-3 control-label">Name</label>
									<div class="col-md-9">
										<input type="text" name="name" class="form-control"
											value="{{ auth()->user()->name }}">
									</div>
								</div>

								<div class="form-group row">
									<label class="col-md-3 control-label">Email</label>
									<div class="col-md-9">
										<input type="email" name="email" class="form-control"
											value="{{ auth()->user()->email }}">
									</div>
								</div>

								<div class="form-group row">
									<label class="col-md-3 control-label">Phone</label>
									<div class="col-md-9">
										<input type="text" name="phone" class="form-control"
											value="{{ auth()->user()->phone }}">
									</div>
								</div>



								<hr>
								<h5>Ganti Password</h5>
								<br>
								<div class="form-group row">
									<label class="col-md-3 control-label">Password Baru</label>
									<div class="col-md-9">
										<input type="password" id="password" name="password" class="form-control"
											placeholder="Password baru">
									</div>
								</div>

								<div class="form-group row">
									<label class="col-md-3 control-label">Konfirmasi Password</label>
									<div class="col-md-9">
										<input type="password" id="password-confirm" name="password_confirmation"
											class="form-control" placeholder="Konfirmasi password">
									</div>
								</div>

								<div class="form-group row">
									<div class="col-md-9 ml-auto">
										<button class="btn btn-primary" type="submit"
											id="change-password">Simpan</button>
										<a href="{{ url('/') }}" class="btn btn-light">Batalkan</a>
									</div>
								</div>
							</form>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('javascripts')
<script>
	$(document).ready(function() {
		$('#change-password').click(function(e) {
			var pass = $('#password').val();
			var confirm = $('#password-confirm').val();
			if (pass && pass !== confirm) {
				e.preventDefault();
				alert('Konfirmasi password tidak sama.');
			}
		});
	});
</script>
@endsection