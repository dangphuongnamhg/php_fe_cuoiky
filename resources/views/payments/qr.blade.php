@extends('layouts.app')
@section('title', 'Thanh toán VNPay — SanGo')
@section('content')

<div class="container py-5" style="max-width:560px;">
    <div class="card-fb text-center p-5">
        {{-- Header --}}
        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;background:linear-gradient(135deg,var(--fb-primary),var(--fb-secondary));color:#fff;font-size:1.5rem;">
            <i class="bi bi-qr-code-scan"></i>
        </div>
        <h1 class="fs-4 fw-bold">Thanh toán qua VNPay</h1>
        <p class="text-muted small mb-4">Quét mã QR bằng ứng dụng ngân hàng hoặc ví VNPay</p>

        {{-- Countdown --}}
        <div class="countdown-badge mx-auto mb-4">
            <i class="bi bi-clock"></i>
            <span id="countdown-timer">14:59</span>
        </div>

        {{-- QR --}}
        <div class="qr-placeholder mb-4">
            <div class="text-center">
                <i class="bi bi-qr-code" style="font-size:5rem;color:var(--fb-primary);opacity:.6;"></i>
                <div class="small text-muted mt-2">QR VNPay</div>
            </div>
        </div>

        {{-- Amount --}}
        <div class="rounded-4 p-3 mb-4" style="background:#f0f4f8;">
            <div class="text-muted" style="font-size:.7rem;">Số tiền thanh toán</div>
            <div class="fs-3 fw-bold" style="color:var(--fb-primary);">{{ number_format($amount ?? 720000) }}đ</div>
        </div>

        {{-- Info --}}
        <div class="rounded-4 border p-3 text-start mb-4" style="font-size:.85rem;">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Mã đơn</span>
                <span class="fw-semibold font-monospace">{{ $bookingCode ?? 'FB-202607-0042' }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Sân</span>
                <span class="fw-semibold">{{ $pitchName ?? 'Sân Bóng Đá A1' }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted">Thời gian</span>
                <span class="fw-semibold">{{ $timeSlot ?? '18:00 – 20:00' }}</span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex flex-column gap-2">
            <a href="{{ url('/payments/result?status=success') }}" class="btn btn-primary rounded-3 py-3 fw-semibold">Tôi đã thanh toán</a>
            <a href="{{ url('/') }}" class="btn btn-light rounded-3 py-2 small text-muted">Hủy giao dịch</a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    var totalSeconds = 15 * 60;
    var timerEl = document.getElementById('countdown-timer');

    var interval = setInterval(function() {
        totalSeconds--;
        if (totalSeconds <= 0) {
            clearInterval(interval);
            timerEl.textContent = 'Hết hạn';
            return;
        }
        var m = Math.floor(totalSeconds / 60);
        var s = totalSeconds % 60;
        timerEl.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    }, 1000);
})();
</script>
@endpush
