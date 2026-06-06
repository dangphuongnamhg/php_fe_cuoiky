@extends('layouts.app')
@section('title', 'Kết quả thanh toán — SanGo')
@section('content')

<div class="container py-5" style="max-width:560px;">
    <div class="card-fb text-center p-5">
        @if(($status ?? 'success') === 'success')
        {{-- SUCCESS --}}
        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;background:#dcfce7;color:#16a34a;font-size:2.2rem;">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <h1 class="fs-4 fw-bold text-success">Thanh toán thành công!</h1>
        <p class="text-muted small mt-2 mb-4">Đơn đặt sân của bạn đã được xác nhận. Kiểm tra email hoặc thông báo để xem chi tiết.</p>

        <div class="rounded-4 border p-3 text-start mb-4" style="font-size:.85rem;">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Mã đơn</span>
                <span class="fw-semibold font-monospace">{{ $bookingCode ?? 'FB-202607-0042' }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Sân</span>
                <span class="fw-semibold">{{ $pitchName ?? 'Sân Bóng Đá A1' }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Ngày</span>
                <span class="fw-semibold">{{ $date ?? '06/06/2026' }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Thời gian</span>
                <span class="fw-semibold">{{ $timeSlot ?? '18:00 – 20:00' }}</span>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between fw-bold">
                <span>Tổng thanh toán</span>
                <span style="color:var(--fb-primary);">{{ number_format($amount ?? 720000) }}đ</span>
            </div>
        </div>

        <div class="d-flex flex-column gap-2">
            <a href="{{ url('/bookings/history') }}" class="btn btn-primary rounded-3 py-3 fw-semibold">Xem lịch sử đặt sân</a>
            <a href="{{ url('/') }}" class="btn btn-outline-primary rounded-3 py-2">Về trang chủ</a>
        </div>

        @else
        {{-- FAILED --}}
        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;background:#fee2e2;color:#dc2626;font-size:2.2rem;">
            <i class="bi bi-x-circle-fill"></i>
        </div>
        <h1 class="fs-4 fw-bold text-danger">Thanh toán thất bại</h1>
        <p class="text-muted small mt-2 mb-4">Giao dịch không thành công. Vui lòng thử lại hoặc chọn phương thức thanh toán khác.</p>

        <div class="d-flex flex-column gap-2">
            <a href="{{ url('/payments/qr') }}" class="btn btn-primary rounded-3 py-3 fw-semibold">Thử lại</a>
            <a href="{{ url('/') }}" class="btn btn-outline-primary rounded-3 py-2">Về trang chủ</a>
        </div>
        @endif
    </div>
</div>

@endsection
