@extends('layouts.app')
@section('title', 'Đặt sân theo giờ — FieldBook')
@section('content')

<div class="container py-4" style="max-width:1280px;">
    <div class="row g-4">
        {{-- LEFT — Pitch Info --}}
        <div class="col-lg-5">
            <div class="card-fb overflow-hidden">
                <img src="{{ $pitch->image_url ?? 'https://images.unsplash.com/photo-1551958219-acbc608c6377?w=1600&q=80' }}" alt="{{ $pitch->name }}" class="w-100" style="height:260px;object-fit:cover;">
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
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT — Date & Slot --}}
        <div class="col-lg-7">
            <div class="card-fb p-4">
                <h2 class="fs-5 fw-semibold">Chọn ngày</h2>
                <div class="d-flex flex-wrap gap-2 mt-3" id="day-selector"></div>

                <h2 class="fs-5 fw-semibold mt-4">Chọn khung giờ</h2>
                <p class="text-muted small">Click ô bắt đầu rồi ô kết thúc. Tối thiểu 1 giờ.</p>

                <div class="slot-grid mt-3" id="slot-grid"></div>

                {{-- Legend --}}
                <div class="d-flex flex-wrap gap-3 mt-3" style="font-size:.75rem;">
                    <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background:#e8f5e9;"></span> Giờ thường</span>
                    <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background:#fff3e0;"></span> Giờ vàng (x1.5)</span>
                    <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background:#f3e8ff;"></span> Cuối tuần (x1.25)</span>
                    <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background:#ffebee;"></span> Đã đặt</span>
                    <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background:#f5f5f5;"></span> Giữ chỗ</span>
                    <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background:var(--fb-primary);"></span> Đang chọn</span>
                </div>

                {{-- Selection summary --}}
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 rounded-4 border p-3 mt-4" style="background:#f8fafc;">
                    <div>
                        <div class="text-muted" style="font-size:.7rem;">Khung giờ đã chọn</div>
                        <div class="fw-semibold small" id="selected-range">Chưa chọn</div>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:.7rem;">Ước tính</div>
                        <div class="fw-bold" style="color:var(--fb-primary);" id="estimated-price">—</div>
                    </div>
                    <a href="#" id="btn-continue" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold disabled">Tiếp tục</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    const pitchId = {{ $pitch->id }};
    const basePrice = {{ $pitch->price_per_hour }};
    const dayNames = ['CN','T2','T3','T4','T5','T6','T7'];

    const dayContainer = document.getElementById('day-selector');
    const days = [];
    for (let i = 1; i <= 6; i++) {
        const d = new Date();
        d.setDate(d.getDate() + i);
        days.push(d);
        const arrayIdx = i - 1;
        const btn = document.createElement('button');
        btn.className = 'day-btn' + (arrayIdx === 0 ? ' active' : '');
        btn.dataset.offset = i;
        btn.innerHTML = '<div class="day-label">' + dayNames[d.getDay()] + '</div><div class="day-date">' + String(d.getDate()).padStart(2, '0') + '/' + String(d.getMonth()+1).padStart(2, '0') + '</div>';
        btn.addEventListener('click', function() { selectDay(arrayIdx); });
        dayContainer.appendChild(btn);
    }

    let currentDay = 0, startSlot = null, endSlot = null;

    function selectDay(idx) {
        currentDay = idx; startSlot = null; endSlot = null;
        dayContainer.querySelectorAll('.day-btn').forEach(function(b,j) { b.classList.toggle('active', j === idx); });
        renderSlots(); updateSummary();
    }

    const bookedSlots = ['18:00','18:30','19:00','19:30'];
    const lockedSlots = ['06:00','06:30'];

    function getSlotClass(time, dayIdx) {
        if (bookedSlots.includes(time) && dayIdx === 2) return 'slot-booked';
        if (lockedSlots.includes(time) && dayIdx === 0) return 'slot-locked';
        var parts = time.split(':').map(Number);
        var totalMins = parts[0] * 60 + parts[1];
        var dow = days[dayIdx].getDay();
        var isPremium = totalMins >= 1050 && totalMins < 1290; // 17:30–21:30
        var isWeekend = dow === 0 || dow === 6;
        if (isPremium) return 'slot-premium';
        if (isWeekend) return 'slot-weekend';
        return 'slot-available';
    }

    function getMultiplier(time, dayIdx) {
        var parts = time.split(':').map(Number);
        var totalMins = parts[0] * 60 + parts[1];
        var dow = days[dayIdx].getDay();
        if (totalMins >= 1050 && totalMins < 1290) return 1.5; // Giờ vàng
        if (dow === 0 || dow === 6) return 1.25; // Cuối tuần
        return 1.0;
    }

    function renderSlots() {
        var grid = document.getElementById('slot-grid');
        grid.innerHTML = '';
        for (var h = 6; h < 24; h++) {
            for (var m = 0; m < 60; m += 30) {
                var time = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
                var cls = getSlotClass(time, currentDay);
                var cell = document.createElement('div');
                cell.className = 'slot-cell ' + cls;
                cell.textContent = time;
                cell.dataset.time = time;
                if (cls !== 'slot-booked' && cls !== 'slot-locked') {
                    (function(t) { cell.addEventListener('click', function() { handleSlotClick(t); }); })(time);
                }
                grid.appendChild(cell);
            }
        }
        highlightRange();
    }

    function handleSlotClick(time) {
        if (!startSlot || (startSlot && endSlot)) { startSlot = time; endSlot = null; }
        else { if (time > startSlot) endSlot = time; else { startSlot = time; endSlot = null; } }
        highlightRange(); updateSummary();
    }

    function highlightRange() {
        document.querySelectorAll('.slot-cell').forEach(function(cell) {
            cell.classList.remove('slot-selected');
            if (startSlot && cell.dataset.time === startSlot) cell.classList.add('slot-selected');
            if (startSlot && endSlot && cell.dataset.time >= startSlot && cell.dataset.time <= endSlot) cell.classList.add('slot-selected');
        });
    }

    function updateSummary() {
        var rangeEl = document.getElementById('selected-range');
        var priceEl = document.getElementById('estimated-price');
        var btnEl = document.getElementById('btn-continue');

        if (startSlot && endSlot) {
            var endParts = endSlot.split(':').map(Number);
            endParts[1] += 30;
            if (endParts[1] >= 60) { endParts[0]++; endParts[1] = 0; }
            var endDisplay = String(endParts[0]).padStart(2,'0') + ':' + String(endParts[1]).padStart(2,'0');
            rangeEl.textContent = startSlot + ' → ' + endDisplay;

            // Tính giá đúng: mỗi block 30p = 0.5h × giá × multiplier
            var price = 0;
            for (var h = 6; h < 24; h++) {
                for (var m = 0; m < 60; m += 30) {
                    var t = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
                    if (t >= startSlot && t <= endSlot) {
                        price += 0.5 * basePrice * getMultiplier(t, currentDay);
                    }
                }
            }
            price = Math.round(price);

            // Validate tối thiểu 1 giờ (2 blocks)
            var sH = parseInt(startSlot.split(':')[0]) + parseInt(startSlot.split(':')[1]) / 60;
            var eH = endParts[0] + endParts[1] / 60;
            var hours = eH - sH;
            if (hours < 1) {
                priceEl.textContent = 'Tối thiểu 1 giờ';
                btnEl.classList.add('disabled'); btnEl.href = '#';
                return;
            }

            priceEl.textContent = price.toLocaleString('vi-VN') + 'đ';
            var d = days[currentDay];
            var dateStr = d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
            btnEl.href = '/bookings/confirm?pitch_id=' + pitchId + '&date=' + dateStr + '&start=' + startSlot + '&end=' + endDisplay + '&price=' + price;
            btnEl.classList.remove('disabled');
        } else if (startSlot) {
            rangeEl.textContent = startSlot + ' → ...';
            priceEl.textContent = '—';
            btnEl.classList.add('disabled'); btnEl.href = '#';
        } else {
            rangeEl.textContent = 'Chưa chọn';
            priceEl.textContent = '—';
            btnEl.classList.add('disabled'); btnEl.href = '#';
        }
    }

    renderSlots();
})();
</script>
@endpush
