@extends('layouts.app')
@section('title', 'Đặt sân cố định tháng — FieldBook')
@section('content')

<div class="container py-4" style="max-width:1280px;">
    <div class="row g-4">
        {{-- LEFT — Pitch Info --}}
        <div class="col-lg-5">
            <div class="card-fb overflow-hidden">
                <img src="{{ $pitch->image_url }}" alt="{{ $pitch->name }}" class="w-100" style="height:260px;object-fit:cover;">
                <div class="p-4">
                    <span class="badge rounded-pill {{ $pitch->pitch_type === 'football' ? 'text-bg-primary' : 'text-bg-info' }} mb-2">{{ $pitch->pitch_type === 'football' ? 'Bóng đá' : 'Pickleball' }}</span>
                    <h1 class="fs-4 fw-bold mb-1">{{ $pitch->name }}</h1>
                    <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                        <div class="text-muted small"><i class="bi bi-geo-alt"></i> {{ $pitch->address ?? 'Đang cập nhật địa chỉ' }}</div>
                        @if($pitch->latitude && $pitch->longitude)
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $pitch->latitude }},{{ $pitch->longitude }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill" style="padding: 2px 10px; font-size: 0.75rem;"><i class="bi bi-cursor"></i> Chỉ đường</a>
                        @endif
                    </div>
                    <p class="text-muted small mt-2">{{ $pitch->description }}</p>
                    <div class="rounded-4 p-3 mt-3" style="background:#f0f4f8;">
                        <div class="text-muted" style="font-size:.7rem;">Giá cơ bản</div>
                        <div class="fs-4 fw-bold" style="color:var(--fb-primary);">{{ number_format($pitch->price_per_hour) }}đ/giờ</div>
                        <div class="small mt-1" style="color:var(--fb-success);">🎁 Trên 3 buổi: Miễn phí trà đá</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT — 4-Step Wizard --}}
        <div class="col-lg-7">
            <div class="card-fb p-4">

                {{-- Step 1 --}}
                <section class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="wizard-step-badge">1</span>
                        <h3 class="fw-semibold mb-0 fs-6">Chọn tháng</h3>
                    </div>
                    <input type="month" id="month-input" class="form-control" style="max-width:220px;">
                </section>

                {{-- Step 2 --}}
                <section class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="wizard-step-badge">2</span>
                        <h3 class="fw-semibold mb-0 fs-6">Chọn thứ trong tuần</h3>
                    </div>
                    <div class="d-flex flex-wrap gap-2" id="weekday-selector">
                        @foreach(['T2','T3','T4','T5','T6','T7','CN'] as $idx => $wd)
                        <button class="day-btn {{ $idx === 2 ? 'active' : '' }}" data-day="{{ $idx }}">{{ $wd }}</button>
                        @endforeach
                    </div>
                </section>

                {{-- Step 3 --}}
                <section class="mb-4" id="dates-section">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="wizard-step-badge">3</span>
                        <h3 class="fw-semibold mb-0 fs-6" id="dates-title">Các ngày T4 trong tháng</h3>
                    </div>
                    <div class="d-flex flex-wrap gap-2" id="dates-list"></div>
                </section>

                {{-- Step 4 --}}
                <section class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="wizard-step-badge">4</span>
                        <h3 class="fw-semibold mb-0 fs-6">Chọn khung giờ cố định</h3>
                    </div>
                    <div class="slot-grid" id="monthly-slot-grid"></div>
                </section>

                {{-- Summary --}}
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 rounded-4 border p-3" style="background:#f8fafc;">
                    <div>
                        <div class="text-muted" style="font-size:.7rem;">Tổng ước tính</div>
                        <div class="fw-bold fs-5" style="color:var(--fb-primary);" id="monthly-total">—</div>
                    </div>
                    <a href="#" id="monthly-continue" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold disabled">Tiếp tục</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    var pitchId = {{ $pitch->id }};
    var basePrice = {{ $pitch->price_per_hour }};
    var weekdayNames = ['T2','T3','T4','T5','T6','T7','CN'];
    var selectedDay = 2;
    var startSlot = null, endSlot = null;

    // Default month
    var now = new Date();
    var nextMonth = new Date(now.getFullYear(), now.getMonth() + 1);
    document.getElementById('month-input').value = nextMonth.getFullYear() + '-' + String(nextMonth.getMonth() + 1).padStart(2, '0');

    // Weekday selector
    document.getElementById('weekday-selector').addEventListener('click', function(e) {
        var btn = e.target.closest('.day-btn');
        if (!btn) return;
        this.querySelectorAll('.day-btn').forEach(function(b) { b.classList.remove('active'); });
        btn.classList.add('active');
        selectedDay = parseInt(btn.dataset.day);
        startSlot = null; endSlot = null;
        renderDates();
        renderSlots();
        updateSummary();
    });

    function renderDates() {
        var monthVal = document.getElementById('month-input').value;
        var parts = monthVal.split('-').map(Number);
        var y = parts[0], m = parts[1];
        document.getElementById('dates-title').textContent = 'Các ngày ' + weekdayNames[selectedDay] + ' trong tháng';
        var list = document.getElementById('dates-list');
        list.innerHTML = '';
        var jsDay = selectedDay < 6 ? selectedDay + 1 : 0;
        var daysInMonth = new Date(y, m, 0).getDate();
        var mockBooked = [17];
        for (var d = 1; d <= daysInMonth; d++) {
            var dt = new Date(y, m - 1, d);
            if (dt.getDay() !== jsDay) continue;
            var booked = mockBooked.includes(d);
            var el = document.createElement('div');
            el.className = 'd-flex align-items-center gap-2 rounded-3 border px-3 py-2 small' + (booked ? ' border-danger bg-danger bg-opacity-10 text-danger' : ' bg-white');
            el.innerHTML = String(d).padStart(2,'0') + '/' + String(m).padStart(2,'0') + (booked ? ' <span class="badge-status status-cancelled">Đã đặt</span>' : '');
            list.appendChild(el);
        }
    }

    function getJsDay() {
        return selectedDay < 6 ? selectedDay + 1 : 0; // T2=1,...T7=6, CN=0
    }

    function getMultiplier(time) {
        var parts = time.split(':').map(Number);
        var totalMins = parts[0] * 60 + parts[1];
        var jsDay = getJsDay();
        if (totalMins >= 1050 && totalMins < 1290) return 1.5; // 17:30–21:30 giờ vàng
        if (jsDay === 0 || jsDay === 6) return 1.25; // Cuối tuần
        return 1.0;
    }

    function renderSlots() {
        var grid = document.getElementById('monthly-slot-grid');
        grid.innerHTML = '';
        var jsDay = getJsDay();
        var isWeekend = jsDay === 0 || jsDay === 6;
        for (var h = 6; h < 24; h++) {
            for (var m = 0; m < 60; m += 30) {
                var time = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
                var totalMins = h * 60 + m;
                var isPremium = totalMins >= 1050 && totalMins < 1290; // 17:30–21:30
                var cls = isPremium ? 'slot-premium' : (isWeekend ? 'slot-weekend' : 'slot-available');
                var cell = document.createElement('div');
                cell.className = 'slot-cell ' + cls;
                cell.textContent = time;
                cell.dataset.time = time;
                (function(t) { cell.addEventListener('click', function() { handleSlotClick(t); }); })(time);
                grid.appendChild(cell);
            }
        }
    }

    function handleSlotClick(time) {
        if (!startSlot || (startSlot && endSlot)) { startSlot = time; endSlot = null; }
        else { if (time > startSlot) endSlot = time; else { startSlot = time; endSlot = null; } }
        highlightRange(); updateSummary();
    }

    function highlightRange() {
        document.querySelectorAll('#monthly-slot-grid .slot-cell').forEach(function(cell) {
            cell.classList.remove('slot-selected');
            if (startSlot && cell.dataset.time === startSlot) cell.classList.add('slot-selected');
            if (startSlot && endSlot && cell.dataset.time >= startSlot && cell.dataset.time <= endSlot) cell.classList.add('slot-selected');
        });
    }

    function countSessions() {
        return document.querySelectorAll('#dates-list > div:not(.border-danger)').length;
    }

    function updateSummary() {
        var totalEl = document.getElementById('monthly-total');
        var btnEl = document.getElementById('monthly-continue');
        if (startSlot && endSlot) {
            var endParts = endSlot.split(':').map(Number);
            endParts[1] += 30;
            if (endParts[1] >= 60) { endParts[0]++; endParts[1] = 0; }
            var endDisplay = String(endParts[0]).padStart(2,'0') + ':' + String(endParts[1]).padStart(2,'0');

            // Validate tối thiểu 1 giờ
            var sH = parseInt(startSlot.split(':')[0]) + parseInt(startSlot.split(':')[1]) / 60;
            var eH = endParts[0] + endParts[1] / 60;
            var hours = eH - sH;
            if (hours < 1) {
                totalEl.textContent = 'Tối thiểu 1 giờ';
                btnEl.classList.add('disabled'); btnEl.href = '#';
                return;
            }

            // Đếm số buổi thực tế (không tính ngày đã đặt)
            var sessions = countSessions();
            if (sessions === 0) {
                totalEl.textContent = 'Không có buổi nào trống';
                btnEl.classList.add('disabled'); btnEl.href = '#';
                return;
            }

            // Tính giá đúng: mỗi block 0.5h × giá × multiplier × số buổi
            var pricePerSession = 0;
            for (var h = 6; h < 24; h++) {
                for (var m = 0; m < 60; m += 30) {
                    var t = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
                    if (t >= startSlot && t <= endSlot) {
                        pricePerSession += 0.5 * basePrice * getMultiplier(t);
                    }
                }
            }
            var total = Math.round(pricePerSession * sessions);

            totalEl.textContent = total.toLocaleString('vi-VN') + 'đ (' + sessions + ' buổi × ' + hours + 'h)';
            btnEl.classList.remove('disabled');
            btnEl.href = '/bookings/confirm?pitch_id=' + pitchId + '&type=monthly&month=' + document.getElementById('month-input').value + '&day=' + selectedDay + '&start=' + startSlot + '&end=' + endDisplay + '&sessions=' + sessions + '&price=' + total;
        } else {
            totalEl.textContent = '—';
            btnEl.classList.add('disabled');
            btnEl.href = '#';
        }
    }

    document.getElementById('month-input').addEventListener('change', renderDates);
    renderDates();
    renderSlots();
})();
</script>
@endpush
