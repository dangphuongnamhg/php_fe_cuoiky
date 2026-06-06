@extends('layouts.app')
@section('title', 'Quên mật khẩu — FieldBook')
@section('content')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card-fb p-4 p-md-5 shadow-sm border-0">
                <div class="text-center mb-4">
                    <h1 class="fs-3 fw-bold mb-2">Quên mật khẩu?</h1>
                    <p class="text-muted small">Nhập địa chỉ email của bạn và chúng tôi sẽ gửi cho bạn một đường dẫn để đặt lại mật khẩu.</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success small py-2 rounded-3">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="form-label small fw-semibold">Email</label>
                        <input type="email" class="form-control form-control-lg rounded-3 fs-6" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg rounded-3 w-100 fw-semibold fs-6 py-2">
                        Gửi link đặt lại mật khẩu
                    </button>
                </form>

                <div class="text-center mt-4 pt-2">
                    <a href="{{ route('login') }}" class="text-decoration-none small text-muted hover-primary">
                        <i class="bi bi-arrow-left me-1"></i> Quay lại đăng nhập
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
