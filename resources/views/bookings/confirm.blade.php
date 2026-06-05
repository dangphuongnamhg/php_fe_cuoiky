@extends('layouts.app')
@section('title', 'Xác nhận đặt sân — FieldBook')
@section('content')

<div class="container py-4" style="max-width:768px;">
    <h1 class="fs-4 fw-bold">Xác nhận đặt sân theo giờ</h1>
    <p class="text-muted small">Kiểm tra thông tin và chọn dịch vụ kèm theo.</p>

    @php
        $type = request('type', 'hourly');
        $sessions = (int) request('sessions', 1);
        $basePrice = (int) request('price', 600000);
        $isMonthly = $type === 'monthly';
        $freeTea = $isMonthly && $sessions > 3;
    @endphp

    {{-- Booking Info --}}
    <div class="card-fb mt-4 p-4">
        <h2 class="fw-semibold fs-6">Thông tin booking</h2>
        <div class="row g-3 mt-2">
            <div class="col-sm-6">
                <div class="text-muted" style="font-size:.7rem;">Sân</div>
                <div class="fw-semibold small">{{ $pitch->name ?? 'Sân Bóng Đá A1' }}</div>
            </div>
            <div class="col-sm-6">
                <div class="text-muted" style="font-size:.7rem;">Ngày</div>
                <div class="fw-semibold small">{{ $date ?? '06/06/2026' }} ({{ $dayOfWeek ?? 'Thứ 7' }})</div>
            </div>
            <div class="col-sm-6">
                <div class="text-muted" style="font-size:.7rem;">Giờ bắt đầu</div>
                <div class="fw-semibold small">{{ $start ?? '18:00' }}</div>
            </div>
            <div class="col-sm-6">
                <div class="text-muted" style="font-size:.7rem;">Giờ kết thúc</div>
                <div class="fw-semibold small">{{ $end ?? '20:00' }}</div>
            </div>
            <div class="col-sm-6">
                <div class="text-muted" style="font-size:.7rem;">Thời lượng</div>
                <div class="fw-semibold small">{{ request('start', '18:00') }} → {{ request('end', '20:00') }}</div>
            </div>
            <div class="col-sm-6">
                <div class="text-muted" style="font-size:.7rem;">Loại đặt sân</div>
                <div class="fw-semibold small">
                    @if($isMonthly)
                        Cố định tháng ({{ $sessions }} buổi)
                    @else
                        Theo giờ (1 buổi)
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Extra Services --}}
    <div class="card-fb mt-4 p-4">
        <h2 class="fw-semibold fs-6">Dịch vụ thêm</h2>
        <div class="d-flex flex-column gap-2 mt-3">
            <label class="d-flex align-items-center justify-content-between rounded-3 border p-3 service-check" style="cursor:pointer;transition:all .2s;">
                <span class="d-flex align-items-center gap-3">
                    <input type="checkbox" class="form-check-input cb-extra" data-price="{{ $freeTea ? 0 : 70000 }}" {{ $freeTea ? 'checked disabled' : '' }} style="accent-color:var(--fb-primary);">
                    <span class="small fw-medium">Trà đá</span>
                </span>
                <span class="small fw-semibold">
                    @if($freeTea)
                        <span class="text-success">Miễn phí 🎁</span>
                    @else
                        70,000đ/buổi
                    @endif
                </span>
            </label>
            <label class="d-flex align-items-center justify-content-between rounded-3 border p-3 service-check" style="cursor:pointer;transition:all .2s;">
                <span class="d-flex align-items-center gap-3">
                    <input type="checkbox" class="form-check-input cb-extra" data-price="50000" style="accent-color:var(--fb-primary);">
                    <span class="small fw-medium">Thuê bóng</span>
                </span>
                <span class="small fw-semibold">50,000đ/buổi</span>
            </label>
            <label class="d-flex align-items-center justify-content-between rounded-3 border p-3 service-check" style="cursor:pointer;transition:all .2s;">
                <span class="d-flex align-items-center gap-3">
                    <input type="checkbox" class="form-check-input cb-extra" data-price="50000" style="accent-color:var(--fb-primary);">
                    <span class="small fw-medium">Áo pit (thuê)</span>
                </span>
                <span class="small fw-semibold">50,000đ/buổi</span>
            </label>
        </div>
    </div>

    {{-- Total --}}
    <div class="card-fb mt-4 p-4">
        <h2 class="fw-semibold fs-6">Tổng tiền</h2>
        <div class="mt-3">
            <div class="d-flex justify-content-between small">
                <span class="text-muted">Giá sân (@if($isMonthly) {{ $sessions }} buổi @else 1 buổi @endif)</span>
                <span class="fw-medium" id="base-price-display">{{ number_format($basePrice) }}đ</span>
            </div>
            <div class="d-flex justify-content-between small mt-2">
                <span class="text-muted">Dịch vụ kèm</span>
                <span class="fw-medium" id="extras-display">0đ</span>
            </div>
            <hr class="my-3">
            <div class="d-flex justify-content-between fw-bold">
                <span>Tổng cộng</span>
                <span style="color:var(--fb-primary);font-size:1.1rem;" id="total-display">{{ number_format($basePrice) }}đ</span>
            </div>
        </div>
        <a href="{{ url('/payments/qr') }}" class="btn btn-primary w-100 rounded-3 py-3 fw-semibold mt-4" id="pay-btn">Thanh toán VNPay</a>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    var basePrice = {{ $basePrice }};
    var sessions = {{ $sessions }};
    var checkboxes = document.querySelectorAll('.cb-extra');

    function recalc() {
        var extras = 0;
        checkboxes.forEach(function(cb) {
            var label = cb.closest('.service-check');
            if (cb.checked) {
                extras += parseInt(cb.dataset.price) * sessions;
                label.style.borderColor = 'var(--fb-primary)';
                label.style.background = '#f0f7ff';
            } else {
                label.style.borderColor = '';
                label.style.background = '';
            }
        });
        document.getElementById('extras-display').textContent = extras.toLocaleString('vi-VN') + 'đ';
        document.getElementById('total-display').textContent = (basePrice + extras).toLocaleString('vi-VN') + 'đ';
    }

    checkboxes.forEach(function(cb) { cb.addEventListener('change', recalc); });
    recalc(); // Run once on load to handle disabled/checked tea
})();
</script>
@endpush
