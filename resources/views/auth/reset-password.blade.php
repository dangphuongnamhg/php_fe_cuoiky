@extends('layouts.app')
@section('title', 'Đặt lại mật khẩu — FieldBook')
@section('content')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card-fb p-4 p-md-5 shadow-sm border-0">
                <div class="text-center mb-4">
                    <h1 class="fs-3 fw-bold mb-2">Đặt lại mật khẩu</h1>
                    <p class="text-muted small">Vui lòng nhập mật khẩu mới (tối thiểu 6 ký tự).</p>
                </div>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    @error('email')
                        <div class="alert alert-danger small py-2 rounded-3 mb-3">
                            {{ $message }}
                        </div>
                    @enderror
                    @error('token')
                        <div class="alert alert-danger small py-2 rounded-3 mb-3">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="mb-3">
                        <label for="password" class="form-label small fw-semibold">Mật khẩu mới</label>
                        <input type="password" class="form-control form-control-lg rounded-3 fs-6" id="password" name="password" required minlength="6">
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label small fw-semibold">Xác nhận mật khẩu</label>
                        <input type="password" class="form-control form-control-lg rounded-3 fs-6" id="password_confirmation" name="password_confirmation" required minlength="6">
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg rounded-3 w-100 fw-semibold fs-6 py-2">
                        Đổi mật khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
