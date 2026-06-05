@extends('layouts.app')
@section('title', 'Thông tin cá nhân — FieldBook')
@section('content')

<div class="container py-4" style="max-width:768px;">
    <h1 class="fs-4 fw-bold">Thông tin cá nhân</h1>
    <p class="text-muted small">Cập nhật thông tin tài khoản của bạn.</p>

    {{-- Avatar & Basic Info --}}
    <div class="card-fb mt-4 p-4">
        <div class="d-flex align-items-center gap-4 mb-4">
            <div class="avatar-circle" style="width:80px;height:80px;font-size:1.6rem;background:var(--fb-primary);">
                {{ strtoupper(substr(Auth::user()->name ?? 'NA', 0, 2)) }}
            </div>
            <div>
                <h2 class="fs-5 fw-bold mb-1">{{ Auth::user()->name ?? 'Nguyễn An' }}</h2>
                <p class="text-muted small mb-0">{{ Auth::user()->email ?? 'an.nguyen@gmail.com' }}</p>
                <span class="badge-status status-confirmed mt-1">Đã xác minh</span>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div class="row g-3">
                <div class="col-sm-6">
                    <label for="name" class="form-label small fw-semibold">Họ tên</label>
                    <input type="text" class="form-control rounded-3" id="name" name="name" value="{{ Auth::user()->name ?? 'Nguyễn An' }}">
                </div>
                <div class="col-sm-6">
                    <label for="email" class="form-label small fw-semibold">Email</label>
                    <input type="email" class="form-control rounded-3" id="email" name="email" value="{{ Auth::user()->email ?? 'an.nguyen@gmail.com' }}">
                </div>
                <div class="col-sm-6">
                    <label for="phone" class="form-label small fw-semibold">Số điện thoại</label>
                    <input type="tel" class="form-control rounded-3" id="phone" name="phone" value="{{ Auth::user()->phone ?? '0912 345 678' }}">
                </div>
                <div class="col-sm-6">
                    <label for="dob" class="form-label small fw-semibold">Ngày sinh</label>
                    <input type="date" class="form-control rounded-3" id="dob" name="dob" value="{{ Auth::user()->dob ?? '1995-04-20' }}">
                </div>
            </div>

            <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold mt-4">Lưu thay đổi</button>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="card-fb mt-4 p-4">
        <h2 class="fw-semibold fs-6">Đổi mật khẩu</h2>
        <form method="POST" action="{{ route('password.update') }}" class="mt-3">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12">
                    <label for="current_password" class="form-label small fw-semibold">Mật khẩu hiện tại</label>
                    <input type="password" class="form-control rounded-3" id="current_password" name="current_password">
                </div>
                <div class="col-sm-6">
                    <label for="password" class="form-label small fw-semibold">Mật khẩu mới</label>
                    <input type="password" class="form-control rounded-3" id="password" name="password">
                </div>
                <div class="col-sm-6">
                    <label for="password_confirmation" class="form-label small fw-semibold">Xác nhận mật khẩu mới</label>
                    <input type="password" class="form-control rounded-3" id="password_confirmation" name="password_confirmation">
                </div>
            </div>

            <button type="submit" class="btn btn-outline-primary rounded-3 px-4 py-2 fw-semibold mt-4">Cập nhật mật khẩu</button>
        </form>
    </div>

    {{-- Stats --}}
    <div class="card-fb mt-4 p-4">
        <h2 class="fw-semibold fs-6">Thống kê</h2>
        <div class="row g-3 mt-2">
            <div class="col-sm-4">
                <div class="rounded-4 p-3 text-center" style="background:#f0f4f8;">
                    <div class="fs-3 fw-bold" style="color:var(--fb-primary);">12</div>
                    <div class="text-muted small">Lượt đặt sân</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="rounded-4 p-3 text-center" style="background:#f0f4f8;">
                    <div class="fs-3 fw-bold" style="color:var(--fb-primary);">3.6M</div>
                    <div class="text-muted small">Tổng chi tiêu</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="rounded-4 p-3 text-center" style="background:#f0f4f8;">
                    <div class="fs-3 fw-bold" style="color:var(--fb-primary);">1</div>
                    <div class="text-muted small">HĐ tháng hiện tại</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Danger zone --}}
    <div class="card-fb mt-4 p-4 border-danger border-opacity-25">
        <h2 class="fw-semibold fs-6 text-danger">Vùng nguy hiểm</h2>
        <p class="text-muted small">Xóa tài khoản sẽ xóa toàn bộ dữ liệu đặt sân. Hành động này không thể hoàn tác.</p>
        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 px-3" onclick="return confirm('Bạn chắc chắn muốn xóa tài khoản?')">Xóa tài khoản</button>
        </form>
    </div>
</div>

@endsection
