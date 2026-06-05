@extends('layouts.admin')
@section('title', 'POS — FieldBook Admin')
@section('content')
<h1 class="h4 fw-bold">Đặt sân tại quầy (POS)</h1>
<p class="text-muted small">Đặt sân cho khách vãng lai, thu tiền mặt trực tiếp.</p>

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="{{ route('admin.pos.store') }}" method="POST" id="posForm">
    @csrf
    <div class="row g-4 mt-2">
        {{-- Left: Customer info + timeslot grid --}}
        <div class="col-lg-8">
            <div class="card-fb p-4">
                <h6 class="fw-semibold">Thông tin khách hàng</h6>
                <div class="row g-3 mt-1">
                    <div class="col-12">
                        <label class="form-label small fw-medium">Khách hàng</label>
                        <input type="text" name="customer_name" class="form-control" placeholder="Tìm theo tên / email hoặc nhập 'Khách vãng lai'" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-medium">Sân</label>
                        <select name="pitch_id" class="form-select" id="posPitchSelect" required>
                            <option value="">— Chọn sân —</option>
                            @foreach($pitches as $p)
                            <option value="{{ $p->id }}" data-price="{{ $p->price_per_hour }}">{{ $p->name }} — {{ number_format($p->price_per_hour) }}đ/giờ</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-medium">Ngày</label>
                        <input type="date" name="booking_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <h6 class="fw-semibold mt-4">Chọn khung giờ</h6>
                <div class="slot-grid mt-3">
                    @php
                        $slots = $timeslots ?? [];
                        $hours = ['06:00','06:30','07:00','07:30','08:00','08:30','09:00','09:30',
                                  '10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30',
                                  '14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30',
                                  '18:00','18:30','19:00','19:30','20:00','20:30','21:00','21:30'];
                    @endphp
                    @foreach($hours as $h)
                    @php
                        $booked = in_array($h, $slots);
                    @endphp
                    <label class="slot-cell {{ $booked ? 'slot-booked' : 'slot-available' }}">
                        <input type="checkbox" name="slots[]" value="{{ $h }}" class="d-none slot-checkbox" {{ $booked ? 'disabled' : '' }}>
                        <span>{{ $h }}</span>
                    </label>
                    @endforeach
                </div>
                <p class="text-muted mt-2" style="font-size:0.7rem;">
                    <span class="d-inline-block rounded me-1" style="width:12px;height:12px;background:var(--fb-primary);vertical-align:middle;"></span> Đã chọn
                    <span class="d-inline-block rounded ms-2 me-1" style="width:12px;height:12px;background:#e8f5e9;vertical-align:middle;"></span> Trống
                    <span class="d-inline-block rounded ms-2 me-1" style="width:12px;height:12px;background:#ffebee;vertical-align:middle;"></span> Đã đặt
                </p>
            </div>
        </div>

        {{-- Right: Services & Payment --}}
        <div class="col-lg-4">
            <div class="card-fb p-4">
                <h6 class="fw-semibold">Dịch vụ &amp; thanh toán</h6>
                <div class="mt-3 d-flex flex-column gap-2">
                    @php
                        $services = $extraServices ?? [
                            ['id' => 'tea', 'label' => 'Trà đá (bình)', 'price' => 70000],
                            ['id' => 'ball', 'label' => 'Thuê bóng', 'price' => 50000],
                            ['id' => 'vest', 'label' => 'Áo pit (bộ)', 'price' => 50000],
                        ];
                    @endphp
                    @foreach($services as $s)
                    <label class="d-flex align-items-center justify-content-between rounded border p-3 cursor-pointer service-item">
                        <span class="d-flex align-items-center gap-2 small">
                            <input type="checkbox" name="services[]" value="{{ $s['id'] }}" data-price="{{ $s['price'] }}" class="form-check-input service-checkbox" style="accent-color:var(--fb-primary);">
                            {{ $s['label'] }}
                        </span>
                        <span class="small fw-medium">{{ number_format($s['price']) }}đ</span>
                    </label>
                    @endforeach
                </div>

                <div class="mt-4 pt-3 border-top small">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Tiền sân</span>
                        <span class="fw-medium" id="posBaseCost">0đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Dịch vụ</span>
                        <span class="fw-medium" id="posServiceCost">0đ</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fw-bold fs-6">
                        <span>Tổng</span>
                        <span style="color:var(--fb-primary);" id="posTotalCost">0đ</span>
                    </div>
                    <input type="hidden" name="total_amount" id="posTotalInput" value="0">
                </div>

                <button type="submit" class="btn w-100 mt-4 d-flex align-items-center justify-content-center gap-2 text-white fw-semibold py-3" style="background:var(--fb-primary);">
                    <i class="bi bi-check-lg"></i> Xác nhận thu tiền mặt
                </button>
                <p class="text-center text-muted mt-2" style="font-size:0.7rem;">
                    Booking sẽ được tạo với trạng thái <strong>Confirmed</strong> ngay lập tức.
                </p>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fmt = (n) => new Intl.NumberFormat('vi-VN').format(n) + 'đ';
    const pitchSelect = document.getElementById('posPitchSelect');
    const slotChecks = document.querySelectorAll('.slot-checkbox');
    const serviceChecks = document.querySelectorAll('.service-checkbox');
    const baseCostEl = document.getElementById('posBaseCost');
    const serviceCostEl = document.getElementById('posServiceCost');
    const totalCostEl = document.getElementById('posTotalCost');
    const totalInput = document.getElementById('posTotalInput');

    function calc() {
        const sel = pitchSelect.options[pitchSelect.selectedIndex];
        const pricePerSlot = sel ? parseInt(sel.dataset.price || 0) / 2 : 0; // 30-min slot = half hour
        let selectedSlots = 0;
        slotChecks.forEach(c => { if (c.checked) selectedSlots++; });
        const base = pricePerSlot * selectedSlots;

        let svc = 0;
        serviceChecks.forEach(c => { if (c.checked) svc += parseInt(c.dataset.price || 0); });

        const total = base + svc;
        baseCostEl.textContent = fmt(base);
        serviceCostEl.textContent = fmt(svc);
        totalCostEl.textContent = fmt(total);
        totalInput.value = total;
    }

    pitchSelect.addEventListener('change', calc);
    slotChecks.forEach(c => {
        c.addEventListener('change', function() {
            this.closest('.slot-cell').classList.toggle('slot-selected', this.checked);
            calc();
        });
    });
    serviceChecks.forEach(c => {
        c.addEventListener('change', function() {
            this.closest('.service-item').classList.toggle('border-primary', this.checked);
            calc();
        });
    });
});
</script>
@endpush
@endsection
