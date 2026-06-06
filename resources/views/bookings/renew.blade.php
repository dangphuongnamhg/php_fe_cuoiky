@extends('layouts.app')
@section('title', 'Gia hạn hợp đồng — SanGo')
@section('content')

<div class="container py-4" style="max-width:768px;">
    <h1 class="fs-4 fw-bold">Gia hạn hợp đồng cố định tháng</h1>
    <p class="text-muted small">Kiểm tra thông tin hợp đồng và chọn kỳ gia hạn.</p>

    {{-- Contract Info --}}
    <div class="card-fb mt-4 p-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge-status status-monthly">Tháng cố định</span>
            <span class="badge-status status-confirmed">Đang hoạt động</span>
        </div>
        <h2 class="fw-semibold fs-6">Thông tin hợp đồng</h2>
        <div class="row g-3 mt-2">
            <div class="col-sm-6">
                <div class="text-muted" style="font-size:.7rem;">Mã hợp đồng</div>
                <div class="fw-semibold small font-monospace">CTR-{{ str_pad($monthlyBooking->id, 4, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="col-sm-6">
                <div class="text-muted" style="font-size:.7rem;">Sân</div>
                <div class="fw-semibold small">{{ $monthlyBooking->pitch->name }}</div>
            </div>
            <div class="col-sm-6">
                <div class="text-muted" style="font-size:.7rem;">Thứ</div>
                <div class="fw-semibold small">Thứ {{ $monthlyBooking->day_of_week + 1 }}</div>
            </div>
            <div class="col-sm-6">
                <div class="text-muted" style="font-size:.7rem;">Khung giờ</div>
                <div class="fw-semibold small">{{ \Carbon\Carbon::parse($monthlyBooking->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($monthlyBooking->end_time)->format('H:i') }}</div>
            </div>
            <div class="col-sm-6">
                <div class="text-muted" style="font-size:.7rem;">Kỳ hiện tại</div>
                <div class="fw-semibold small">Tháng {{ \Carbon\Carbon::parse($monthlyBooking->month_start)->format('m/Y') }}</div>
            </div>
            <div class="col-sm-6">
                <div class="text-muted" style="font-size:.7rem;">Hết hạn</div>
                <div class="fw-semibold small text-danger">{{ \Carbon\Carbon::parse($monthlyBooking->month_start)->endOfMonth()->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    {{-- Renewal Options --}}
    <div class="card-fb mt-4 p-4">
        <h2 class="fw-semibold fs-6">Chọn kỳ gia hạn</h2>
        <div class="d-flex flex-column gap-2 mt-3">
            @foreach([
                ['months' => 1, 'label' => '1 tháng', 'price' => 3600000, 'discount' => null],
                ['months' => 3, 'label' => '3 tháng', 'price' => 10440000, 'discount' => 'Giảm 3%'],
                ['months' => 6, 'label' => '6 tháng', 'price' => 19440000, 'discount' => 'Giảm 10%'],
            ] as $opt)
            <label class="d-flex align-items-center justify-content-between rounded-3 border p-3 renew-option" style="cursor:pointer;transition:all .2s;">
                <span class="d-flex align-items-center gap-3">
                    <input type="radio" name="renewal_period" class="form-check-input" value="{{ $opt['months'] }}" data-price="{{ $opt['price'] }}" {{ $opt['months'] === 1 ? 'checked' : '' }} style="accent-color:var(--fb-primary);">
                    <span>
                        <span class="small fw-medium">{{ $opt['label'] }}</span>
                        @if($opt['discount'])
                        <span class="badge rounded-pill text-bg-success ms-2" style="font-size:.65rem;">{{ $opt['discount'] }}</span>
                        @endif
                    </span>
                </span>
                <span class="fw-bold small" style="color:var(--fb-primary);">{{ number_format($opt['price']) }}đ</span>
            </label>
            @endforeach
        </div>
    </div>

    {{-- Summary --}}
    <div class="card-fb mt-4 p-4">
        <h2 class="fw-semibold fs-6">Tóm tắt gia hạn</h2>
        <div class="mt-3" style="font-size:.85rem;">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Kỳ gia hạn</span>
                <span class="fw-semibold" id="renew-period">1 tháng</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Kỳ mới</span>
                <span class="fw-semibold" id="renew-new-period">Tháng 07/2026</span>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between fw-bold">
                <span>Tổng thanh toán</span>
                <span style="color:var(--fb-primary);font-size:1.1rem;" id="renew-total">3,600,000đ</span>
            </div>
        </div>
        <form method="POST" action="{{ route('bookings.renew.store', $monthlyBooking->id) }}">
            @csrf
            <input type="hidden" name="renewal_period" id="form-renewal-period" value="1">
            <input type="hidden" name="total_price" id="form-total-price" value="3600000">
            <button type="submit" class="btn btn-primary w-100 rounded-3 py-3 fw-semibold mt-4">Thanh toán gia hạn</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    var radios = document.querySelectorAll('.renew-option input[type=radio]');
    var labels = {1:'1 tháng', 3:'3 tháng', 6:'6 tháng'};
    
    // Calculate new periods based on current contract expiration
    var currentExpires = new Date("{{ \Carbon\Carbon::parse($monthlyBooking->month_start)->endOfMonth()->toDateString() }}");
    var nextMonth = new Date(currentExpires);
    nextMonth.setDate(nextMonth.getDate() + 1);
    
    function formatDate(date) {
        return ('0' + (date.getMonth()+1)).slice(-2) + '/' + date.getFullYear();
    }
    
    var periods = {
        1: 'Tháng ' + formatDate(nextMonth),
        3: formatDate(nextMonth) + ' – ' + formatDate(new Date(nextMonth.getFullYear(), nextMonth.getMonth()+2, 1)),
        6: formatDate(nextMonth) + ' – ' + formatDate(new Date(nextMonth.getFullYear(), nextMonth.getMonth()+5, 1))
    };

    function update() {
        var selected = document.querySelector('.renew-option input[type=radio]:checked');
        var months = parseInt(selected.value);
        var price = parseInt(selected.dataset.price);

        document.getElementById('renew-period').textContent = labels[months];
        document.getElementById('renew-new-period').textContent = periods[months];
        document.getElementById('renew-total').textContent = price.toLocaleString('vi-VN') + 'đ';
        
        document.getElementById('form-renewal-period').value = months;
        document.getElementById('form-total-price').value = price;

        document.querySelectorAll('.renew-option').forEach(function(label) {
            var radio = label.querySelector('input[type=radio]');
            if (radio.checked) {
                label.style.borderColor = 'var(--fb-primary)';
                label.style.background = '#f0f7ff';
            } else {
                label.style.borderColor = '';
                label.style.background = '';
            }
        });
    }

    radios.forEach(function(radio) { radio.addEventListener('change', update); });
    update();
})();
</script>
@endpush
