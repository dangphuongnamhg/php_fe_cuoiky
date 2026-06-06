@extends('layouts.app')
@section('title', 'Đăng ký — FieldBook')
@section('content')
<div class="container-fluid min-vh-100 d-flex p-0">
    <div class="row g-0 flex-grow-1">
        <div class="col-lg-5 d-flex align-items-center justify-content-center p-4 p-lg-5">
            <div style="max-width:400px;width:100%;">
                <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 mb-4 text-decoration-none">
                    <svg width="48" height="48" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="40" height="40" rx="12" fill="url(#fb-grad)"/>
                        <path d="M12 20C12 15.5817 15.5817 12 20 12C24.4183 12 28 15.5817 28 20C28 24.4183 24.4183 28 20 28C15.5817 28 12 24.4183 12 20Z" stroke="white" stroke-width="2.5"/>
                        <path d="M15 20L25 20" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M20 15L20 25" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        <defs>
                            <linearGradient id="fb-grad" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#22C55E"/>
                                <stop offset="1" stop-color="#059669"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="d-flex flex-column justify-content-center">
                        <span class="fw-bold fs-2" style="color:var(--fb-primary); line-height: 1.1; letter-spacing: -0.5px;">Field<span style="color:#22C55E;">Book</span></span>
                        <span class="fw-semibold text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px; text-transform: uppercase;">Hệ thống đặt sân hàng đầu tại Hà Nội</span>
                    </div>
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
                        <input type="password" name="password" id="register-password" class="form-control rounded-3" placeholder="••••••" required minlength="6" oninput="checkPasswordLength(this, 'pwd-error')">
                        <div id="pwd-error" class="text-danger small mt-1" style="display:none;"><i class="bi bi-exclamation-circle me-1"></i>Mật khẩu phải từ 6 ký tự trở lên.</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-medium">Xác nhận mật khẩu</label>
                        <input type="password" name="password_confirmation" id="register-password-confirm" class="form-control rounded-3" placeholder="••••••" required minlength="6" oninput="checkPasswordLength(this, 'pwd-confirm-error')">
                        <div id="pwd-confirm-error" class="text-danger small mt-1" style="display:none;"><i class="bi bi-exclamation-circle me-1"></i>Mật khẩu phải từ 6 ký tự trở lên.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">Đăng ký</button>
                </form>
                <p class="text-center mt-4 small text-muted">Đã có tài khoản? <a href="{{ route('login') }}" class="fw-semibold text-decoration-none" style="color:var(--fb-primary);">Đăng nhập</a></p>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block" style="background:url('https://images.unsplash.com/photo-1459865264687-595d652de67e?w=1600&q=80') center/cover;"></div>
    </div>
</div>

@push('scripts')
<script>
function checkPasswordLength(input, errorId) {
    var errorDiv = document.getElementById(errorId);
    if (input.value.length > 0 && input.value.length < 6) {
        errorDiv.style.display = 'block';
    } else {
        errorDiv.style.display = 'none';
    }
}
</script>
@endpush
@endsection
