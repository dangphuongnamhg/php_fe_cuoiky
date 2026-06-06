@extends('layouts.app')
@section('title', 'Đăng ký — SanGo')
@section('content')
<div class="container-fluid min-vh-100 d-flex p-0">
    <div class="row g-0 flex-grow-1">
        <div class="col-lg-5 d-flex align-items-center justify-content-center p-4 p-lg-5">
            <div style="max-width:400px;width:100%;">
                <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 mb-4 text-decoration-none">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold" style="width:40px;height:40px;background:var(--fb-primary);">FB</div>
                    <span class="fw-bold fs-4" style="color:var(--fb-primary);">SanGo</span>
                </a>
                <h2 class="fw-bold mb-1">Tạo tài khoản</h2>
                <p class="text-muted small mb-4">Đăng ký để đặt sân nhanh chóng và tiện lợi.</p>
                @if($errors->any())
                    <div class="alert alert-danger py-2 small">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                @endif
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Họ và tên</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control rounded-3" placeholder="Nguyễn Văn A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control rounded-3" placeholder="you@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Mật khẩu</label>
                        <input type="password" name="password" class="form-control rounded-3" placeholder="••••••••" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-medium">Xác nhận mật khẩu</label>
                        <input type="password" name="password_confirmation" class="form-control rounded-3" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">Đăng ký</button>
                </form>
                <p class="text-center mt-4 small text-muted">Đã có tài khoản? <a href="{{ route('login') }}" class="fw-semibold text-decoration-none" style="color:var(--fb-primary);">Đăng nhập</a></p>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block" style="background:url('https://images.unsplash.com/photo-1459865264687-595d652de67e?w=1600&q=80') center/cover;"></div>
    </div>
</div>
@endsection
