@extends('layouts.admin')
@section('title', 'POS — SanGo Admin')
@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Đặt sân tại quầy (POS)</h1>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<!-- Global SweetAlert handles this -->
@endif
@if($errors->any())
<div class="alert alert-danger mt-3">
    <ul class="mb-0">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.pos.store') }}" method="POST" id="posForm">
    @csrf
    <div class="row g-4 mt-2">
        {{-- Left: Customer info + timeslot grid --}}
        <div class="col-lg-8">
            <div class="card-fb p-4">
                <h6 class="fw-semibold">Thông tin khách hàng</h6>
                <div class="row g-3 mt-1 align-items-end">
                    <div class="col-12 col-md-6 position-relative" id="customerSearchContainer">
                        <label class="form-label fw-medium small">Khách hàng (Tên/SĐT) <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" name="customer_name" id="customerSearchInput" class="form-control border-start-0 ps-0" placeholder="Nhập tên khách..." value="{{ request('customer_name') }}" autocomplete="off" required>
                            <input type="hidden" name="user_id" id="posUserId" value="{{ request('user_id') }}">
                        </div>
                        <!-- Autocomplete Dropdown -->
                        <ul class="dropdown-menu w-100 shadow-sm border-0 mt-1" id="customerSearchDropdown" style="max-height: 250px; overflow-y: auto; display: none; position: absolute; z-index: 1050;">
                        </ul>
                        <!-- Email Input for Fixed Booking -->
                        <div class="mt-2 d-none" id="customerEmailContainer">
                            <label class="form-label fw-medium small mb-1 text-muted">Email (Bắt buộc để tạo tài khoản đặt tháng)</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="customer_email" id="customerEmailInput" class="form-control border-start-0 ps-0" placeholder="Nhập email..." value="{{ request('customer_email') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-medium text-muted">Loại đặt sân</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-tags-fill"></i></span>
                            <select name="booking_type" id="posBookingType" class="form-select border-start-0 ps-0">
                                <option value="single" {{ request('booking_type') == 'single' ? 'selected' : '' }}>Đặt một buổi</option>
                                <option value="fixed" {{ request('booking_type') == 'fixed' ? 'selected' : '' }}>Đặt cố định</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label small fw-medium text-muted">Sân</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-geo-alt-fill"></i></span>
                            <select name="pitch_id" class="form-select border-start-0 ps-0" id="posPitchSelect" required>
                                <option value="">— Chọn sân —</option>
                                @foreach($pitches as $p)
                                <option value="{{ $p->id }}" {{ ($selectedPitchId ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }} — {{ number_format($p->price_per_hour) }}đ/giờ</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 {{ request('booking_type', 'single') == 'single' ? '' : 'd-none' }}" id="colSingleDate">
                        <label class="form-label small fw-medium text-muted" id="lblDateSelect">Ngày</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-calendar-check-fill"></i></span>
                            <input type="date" name="booking_date" id="posDateSelect" class="form-control border-start-0 ps-0 fw-medium text-primary" value="{{ request('booking_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-sm-4 {{ request('booking_type') == 'fixed' ? '' : 'd-none' }}" id="colFixedMonth">
                        <label class="form-label small fw-medium text-muted">Tháng</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-calendar-month-fill"></i></span>
                            <input type="month" name="booking_month" id="posMonthSelect" class="form-control border-start-0 ps-0 fw-medium text-primary" value="{{ request('booking_month', date('Y-m')) }}" min="{{ date('Y-m') }}">
                        </div>
                    </div>
                    <div class="col-12 {{ request('booking_type') == 'fixed' ? '' : 'd-none' }}" id="colFixedDays">
                        <label class="form-label small fw-medium text-muted mb-2">Chọn Thứ (trong tuần)</label>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $days = [1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7', 0 => 'CN'];
                                $selectedDays = request('days_of_week', []);
                            @endphp
                            @foreach($days as $val => $label)
                            <div class="form-check form-check-inline shadow-sm bg-white border rounded px-3 py-2 m-0 day-checkbox-wrap" style="cursor:pointer;" onclick="const cb=document.getElementById('day_{{ $val }}'); cb.checked=!cb.checked; cb.dispatchEvent(new Event('change'));">
                                <input class="form-check-input pos-day-checkbox" type="checkbox" name="days_of_week[]" id="day_{{ $val }}" value="{{ $val }}" {{ in_array($val, $selectedDays) ? 'checked' : '' }} onclick="event.stopPropagation();">
                                <label class="form-check-label small fw-semibold" for="day_{{ $val }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div id="dynamic-content" style="transition: opacity 0.2s;">
                    @if(!empty($selectedPitchId))
                    @if(\Carbon\Carbon::parse($selectedDate)->startOfDay() < \Carbon\Carbon::today())
                    <div class="mt-4 p-4 text-center rounded bg-light border">
                        <i class="bi bi-calendar-x fs-3 text-danger"></i>
                        <p class="text-danger mt-2 mb-0">Không thể đặt sân cho ngày trong quá khứ.</p>
                    </div>
                    @else
                    <div id="grid-container" class="mt-4 border-top pt-4">
                        <div class="row g-4">
                            <div class="col-md-7 col-lg-8">
                                <h6 class="fw-semibold">Chọn khung giờ</h6>
                                <div class="slot-grid mt-3" id="slot-grid"></div>

                                <div class="d-flex flex-wrap gap-3 mt-3" style="font-size:.75rem;">
                                    <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background:#e8f5e9;"></span> Giờ thường</span>
                                    <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background:#fff3e0;"></span> Giờ vàng (x1.5)</span>
                                    <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background:#f3e8ff;"></span> Cuối tuần (x1.25)</span>
                                    <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background:#ffebee;"></span> Đã đặt</span>
                                    <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background:var(--fb-primary);"></span> Đang chọn</span>
                                </div>
                            </div>
                            <div class="col-md-5 col-lg-4 border-start">
                                <h6 class="fw-semibold mb-3">Dịch vụ đi kèm</h6>
                                <div class="d-flex flex-column gap-2">
                                    @php
                                        $services = [
                                            ['id' => 'tea', 'label' => 'Trà đá (ca)', 'price' => \App\Models\Setting::get('tea_price', 10000)],
                                            ['id' => 'ball', 'label' => 'Thuê bóng', 'price' => \App\Models\Setting::get('ball_price', 30000)],
                                            ['id' => 'vest', 'label' => 'Thuê áo pit (bộ)', 'price' => \App\Models\Setting::get('bib_price', 50000)],
                                        ];
                                    @endphp
                                    @foreach($services as $s)
                                    <div class="d-flex align-items-center justify-content-between rounded border p-2 service-item" style="font-size: 0.8rem;">
                                        <label class="d-flex align-items-center gap-2 cursor-pointer mb-0" style="user-select: none; width: 100%;">
                                            <input type="checkbox" name="services[]" value="{{ $s['id'] }}" data-price="{{ $s['price'] }}" class="form-check-input service-checkbox m-0" style="accent-color:var(--fb-primary);">
                                            <span class="text-nowrap service-label-text" style="flex-grow: 1;">{{ $s['label'] }}</span>
                                        </label>
                                        
                                        {{-- Quantity Selector --}}
                                        <div class="d-flex align-items-center bg-light rounded-pill border overflow-hidden ms-2" style="min-width: 80px; height: 30px; {!! $s['id'] === 'tea' ? 'visibility:hidden;' : '' !!}">
                                            <button type="button" class="btn btn-light btn-minus text-muted border-0 p-0 h-100 d-flex align-items-center justify-content-center bg-transparent hover-bg" style="width: 26px;" disabled>
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <input type="number" name="service_qty[{{ $s['id'] }}]" class="form-control text-center border-0 bg-transparent p-0 service-qty fw-medium no-spin" value="1" min="1" style="width: 28px; box-shadow: none; font-size: 0.8rem;" disabled>
                                            <button type="button" class="btn btn-light btn-plus text-muted border-0 p-0 h-100 d-flex align-items-center justify-content-center bg-transparent hover-bg" style="width: 26px;" disabled>
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                <div id="hidden-slots-container"></div>
                @else
                <div class="mt-4 p-4 text-center rounded bg-light border">
                    <i class="bi bi-calendar3 fs-3 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Vui lòng chọn Sân và Ngày để tải danh sách khung giờ.</p>
                </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Billing --}}
        <div class="col-lg-4">
            <div class="card-fb p-4">
                <h6 class="fw-semibold">Hóa đơn &amp; Thanh toán</h6>

                <div class="mt-3 pt-2 small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tiền sân</span>
                        <span class="fw-medium" id="posBaseCost">0đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Dịch vụ đi kèm</span>
                        <span class="fw-medium" id="posServiceCost">0đ</span>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Tổng</span>
                        <span style="color:var(--fb-primary);" id="posTotalCost">0đ</span>
                    </div>
                    <input type="hidden" name="total_amount" id="posTotalInput" value="0">
                    <input type="hidden" name="payment_method" id="paymentMethodInput" value="cash">
                </div>

                <button type="button" id="btn-submit-pos" class="btn w-100 mt-4 d-flex align-items-center justify-content-center gap-2 text-white fw-semibold py-3 disabled" style="background:var(--fb-primary);" onclick="openPaymentModal()">
                    <i class="bi bi-wallet2"></i> Thanh toán
                </button>
            </div>
        </div>
    </div>
    <div id="pos-data-store" class="d-none"
         data-base-price="{{ optional($pitches->firstWhere('id', $selectedPitchId ?? null))->price_per_hour ?? 0 }}"
         data-booked="{{ json_encode($bookedSlots ?? []) }}"
         data-valid-dates="{{ json_encode(isset($validDates) ? array_map(function($d) { return $d->toDateString(); }, $validDates) : []) }}"
         data-matches="{{ $matchesCount ?? 1 }}"
         data-type="{{ request('booking_type', 'single') }}"
         data-single-date="{{ $selectedDate ?? date('Y-m-d') }}">
    </div>
</form>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Phương thức thanh toán</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="text-center mb-4">
                    <div class="text-muted small fw-medium mb-1">Tổng số tiền cần thanh toán</div>
                    <div class="fs-1 fw-bold text-primary" id="modalTotalAmount">0đ</div>
                </div>

                <!-- Payment Methods Toggle -->
                <ul class="nav nav-pills nav-fill bg-light p-1 rounded-pill mb-4" id="paymentTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill fw-medium d-flex align-items-center justify-content-center gap-2 py-2" id="tab-cash" data-bs-toggle="pill" data-bs-target="#pane-cash" type="button" role="tab" onclick="document.getElementById('paymentMethodInput').value='cash'">
                            <i class="bi bi-cash-stack"></i> Tiền mặt
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill fw-medium d-flex align-items-center justify-content-center gap-2 py-2" id="tab-qr" data-bs-toggle="pill" data-bs-target="#pane-qr" type="button" role="tab" onclick="document.getElementById('paymentMethodInput').value='transfer'; generateQR();">
                            <i class="bi bi-qr-code-scan"></i> Mã QR
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="paymentTabContent">
                    <!-- Cash Pane -->
                    <div class="tab-pane fade show active text-center" id="pane-cash" role="tabpanel">
                        <div class="p-4 bg-light rounded-3 mb-3 border">
                            <i class="bi bi-wallet2 text-success" style="font-size: 3rem;"></i>
                            <p class="mt-3 mb-0 text-muted small">Thu tiền mặt trực tiếp từ khách hàng tại quầy.</p>
                        </div>
                        <button type="button" class="btn btn-success w-100 py-3 fw-bold rounded-pill" onclick="document.getElementById('posForm').submit()">
                            <i class="bi bi-check-circle me-2"></i> Xác nhận đã thu tiền mặt
                        </button>
                    </div>
                    <!-- QR Pane -->
                    <div class="tab-pane fade text-center" id="pane-qr" role="tabpanel">
                        <div class="p-3 bg-light rounded-3 mb-3 border d-flex flex-column align-items-center justify-content-center">
                            <div id="qrLoading" class="spinner-border text-primary my-4" role="status"></div>
                            <img id="qrImage" src="" alt="QR Code" class="img-fluid rounded d-none" style="max-width: 220px;">
                            <p class="mt-3 mb-0 text-muted small">Khách hàng quét mã QR qua ứng dụng ngân hàng.</p>
                        </div>
                        <button type="button" class="btn btn-primary w-100 py-3 fw-bold rounded-pill" onclick="document.getElementById('posForm').submit()">
                            <i class="bi bi-check-circle me-2"></i> Xác nhận khách đã chuyển
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
.no-spin::-webkit-inner-spin-button, .no-spin::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
.no-spin { -moz-appearance: textfield; }
.hover-bg:not(:disabled):hover { background: #e2e8f0 !important; }
.hover-bg:disabled { opacity: 0.4; cursor: not-allowed; }
.slot-cell { cursor: pointer; padding: 10px; border: 1px solid #ddd; text-align: center; border-radius: 4px; }
.slot-cell.booked { background: #ffebee; cursor: not-allowed; }
.slot-checkbox { display: none; }
.slot-checkbox:checked + .slot-cell { background: var(--fb-primary); color: white; border-color: var(--fb-primary); }
</style>
<script>
let startSlot = null;
let endSlot = null;
let basePriceHour = 0;
let bookedSlots = [];
let validDates = [];
let matchesCount = 1;
let bookingType = 'single';
let singleDateStr = '';

const fmt = (n) => new Intl.NumberFormat('vi-VN').format(n) + 'đ';

function syncData() {
    const store = document.getElementById('pos-data-store');
    if (!store) return false;
    basePriceHour = parseInt(store.dataset.basePrice) || 0;
    bookedSlots = JSON.parse(store.dataset.booked || '[]');
    validDates = JSON.parse(store.dataset.validDates || '[]');
    matchesCount = parseInt(store.dataset.matches) || 1;
    bookingType = store.dataset.type;
    singleDateStr = store.dataset.singleDate;
    return true;
}

// User Autocomplete
function initAutocomplete() {
    const customerInput = document.getElementById('customerSearchInput');
    const customerDropdown = document.getElementById('customerSearchDropdown');
    const customerIdInput = document.getElementById('posUserId');
    const allUsers = @json($users ?? []);

    if (customerInput && customerDropdown && !customerInput.dataset.autocompleteBound) {
        customerInput.dataset.autocompleteBound = '1';

        function removeAccents(str) {
            if (!str) return '';
            return String(str).normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
        }

        customerInput.addEventListener('input', function() {
            customerIdInput.value = '';
            
            const rawVal = this.value.trim();
            const val = removeAccents(rawVal);
            customerDropdown.innerHTML = '';
            
            if (val.length === 0) {
                customerDropdown.style.display = 'none';
                return;
            }

            const matches = allUsers.filter(u => {
                return removeAccents(u.name).includes(val) || removeAccents(u.email).includes(val);
            }).slice(0, 8);

            if (matches.length > 0) {
                matches.forEach(match => {
                    const li = document.createElement('li');
                    const a = document.createElement('a');
                    a.className = 'dropdown-item small py-2 text-wrap cursor-pointer';
                    a.href = 'javascript:void(0)';
                    
                    let emailStr = match.email && !match.email.includes('@khach.local') ? ` - <span class="text-muted">${match.email}</span>` : '';
                    let text = `<strong>${match.name}</strong>${emailStr}`;
                    a.innerHTML = text;
                    
                    a.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        customerInput.value = match.name;
                        customerIdInput.value = match.id;
                        customerDropdown.style.display = 'none';
                        toggleEmailRequirement();
                    });
                    
                    li.appendChild(a);
                    customerDropdown.appendChild(li);
                });
                customerDropdown.style.display = 'block';
            } else {
                customerDropdown.style.display = 'none';
            }
            toggleEmailRequirement();
        });

        document.addEventListener('mousedown', function(e) {
            if (customerDropdown.style.display === 'block' && !customerInput.contains(e.target) && !customerDropdown.contains(e.target)) {
                customerDropdown.style.display = 'none';
            }
        });
        
        customerInput.addEventListener('focus', function() {
            if (this.value.trim().length > 0 && customerDropdown.children.length > 0) {
                customerDropdown.style.display = 'block';
            }
        });
        
        document.getElementById('posBookingType').addEventListener('change', function() {
            bookingType = this.value;
            toggleEmailRequirement();
        });
    }
}

function toggleEmailRequirement() {
    const emailContainer = document.getElementById('customerEmailContainer');
    const emailInput = document.getElementById('customerEmailInput');
    const userId = document.getElementById('posUserId').value;
    const nameInput = document.getElementById('customerSearchInput').value;

    if (bookingType === 'fixed' && !userId && nameInput.trim().length > 0) {
        emailContainer.classList.remove('d-none');
        emailInput.required = true;
    } else {
        emailContainer.classList.add('d-none');
        emailInput.required = false;
    }
}

function bindServiceEvents() {
    document.querySelectorAll('.service-item').forEach(item => {
        const chk = item.querySelector('.service-checkbox');
        const qty = item.querySelector('.service-qty');
        const btnMinus = item.querySelector('.btn-minus');
        const btnPlus = item.querySelector('.btn-plus');
        if(!chk) return;

        // Remove old listeners to prevent duplicates
        const newChk = chk.cloneNode(true);
        chk.parentNode.replaceChild(newChk, chk);
        const newMinus = btnMinus.cloneNode(true);
        btnMinus.parentNode.replaceChild(newMinus, btnMinus);
        const newPlus = btnPlus.cloneNode(true);
        btnPlus.parentNode.replaceChild(newPlus, btnPlus);
        const newQty = qty.cloneNode(true);
        qty.parentNode.replaceChild(newQty, qty);

        newChk.addEventListener('change', function() {
            item.classList.toggle('border-primary', this.checked);
            newQty.disabled = !this.checked;
            newMinus.disabled = !this.checked;
            newPlus.disabled = !this.checked;
            if(!this.checked) newQty.value = 1;
            calc();
        });
        newMinus.addEventListener('click', () => { if(newQty.value > 1) { newQty.value--; calc(); } });
        newPlus.addEventListener('click', () => { newQty.value++; calc(); });
        newQty.addEventListener('input', () => { if(newQty.value < 1) newQty.value = 1; calc(); });
    });
}

function getSlotClass(time) {
    if (bookedSlots.includes(time)) return 'booked';
    var parts = time.split(':').map(Number);
    var totalMins = parts[0] * 60 + parts[1];
    var isPremium = totalMins >= 1050 && totalMins < 1290;
    if (isPremium) return 'premium';

    var isWeekend = false;
    if (bookingType === 'single') {
        var dObj = new Date(singleDateStr);
        var dow = dObj.getDay();
        isWeekend = (dow === 0 || dow === 6);
    } else {
        if (validDates.length > 0) {
            isWeekend = validDates.every(dStr => {
                const dow = new Date(dStr).getDay();
                return dow === 0 || dow === 6;
            });
        }
    }

    if (isWeekend) return 'weekend';
    return 'available';
}

function renderSlots() {
    var grid = document.getElementById('slot-grid');
    if (!grid) return;
    grid.innerHTML = '';
    grid.className = 'd-grid gap-2'; 
    grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(80px, 1fr))';
    for (var h = 6; h < 24; h++) {
        for (var m = 0; m < 60; m += 30) {
            var time = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
            var cls = getSlotClass(time);
            var cell = document.createElement('div');
            cell.className = 'slot-cell ' + cls;
            cell.textContent = time;
            cell.dataset.time = time;
            if (cls !== 'booked') {
                (function(t) { cell.addEventListener('click', function() { handleSlotClick(t); }); })(time);
            }
            grid.appendChild(cell);
        }
    }
    highlightRange();
}

function handleSlotClick(time) {
    if (!startSlot || (startSlot && endSlot)) { 
        startSlot = time; 
        endSlot = null; 
    }
    else { 
        if (time > startSlot) {
            endSlot = time; 
        } else { 
            startSlot = time; 
            endSlot = null; 
        } 
    }
    highlightRange(); 
    calc();
}

function highlightRange() {
    document.querySelectorAll('.slot-cell').forEach(function(cell) {
        cell.classList.remove('selected', 'selected-start', 'selected-end');
        cell.style.background = '';
        cell.style.color = '';
        cell.style.borderColor = '';
        
        var t = cell.dataset.time;
        var isBooked = cell.classList.contains('booked');
        if (isBooked) return;

        var isSelected = false;
        if (startSlot && t === startSlot) { isSelected = true; cell.classList.add('selected-start'); }
        if (endSlot && t === endSlot) { isSelected = true; cell.classList.add('selected-end'); }
        if (startSlot && endSlot && t >= startSlot && t <= endSlot) { isSelected = true; cell.classList.add('selected'); }
        
        if (isSelected) {
            cell.style.background = 'var(--fb-primary)';
            cell.style.color = 'white';
            cell.style.borderColor = 'var(--fb-primary)';
        }
    });
}

function calc() {
    let totalBase = 0;
    let blocks = 0;
    const baseCostEl = document.getElementById('posBaseCost');
    const serviceCostEl = document.getElementById('posServiceCost');
    const totalCostEl = document.getElementById('posTotalCost');
    const totalInput = document.getElementById('posTotalInput');
    const btnSubmit = document.getElementById('btn-submit-pos');
    
    const hiddenContainer = document.getElementById('hidden-slots-container');
    if (!hiddenContainer) {
        // Not in grid mode, calculate only services
        let svc = 0;
        document.querySelectorAll('.service-item').forEach(item => {
            const chk = item.querySelector('.service-checkbox');
            const qty = item.querySelector('.service-qty');
            
            if (chk && chk.value === 'tea') {
                const labelSpan = item.querySelector('.service-label-text');
                if (bookingType === 'fixed' && matchesCount >= 3) {
                    chk.dataset.price = "0";
                    if (labelSpan) labelSpan.innerHTML = 'Trà đá <span class="badge bg-success ms-1">Miễn phí 🎁</span>';
                } else {
                    chk.dataset.price = "70000";
                    if (labelSpan) labelSpan.innerHTML = 'Trà đá';
                }
            }

            if (chk && chk.checked) {
                svc += parseInt(chk.dataset.price || 0) * parseInt(qty.value || 1);
            }
        });
        const total = svc * matchesCount;
        if(serviceCostEl) serviceCostEl.innerHTML = fmt(svc) + (matchesCount > 1 ? ` <span class="text-muted small">x${matchesCount}</span>` : '');
        if(totalCostEl) totalCostEl.textContent = fmt(total);
        const mt = document.getElementById('modalTotalAmount');
        if(mt) mt.textContent = fmt(total);
        if(totalInput) totalInput.value = total;
        return;
    }

      hiddenContainer.innerHTML = '';
      
      if (startSlot) {
          for (var h = 6; h < 24; h++) {
              for (var m = 0; m < 60; m += 30) {
                var t = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
                
                let isSelected = false;
                if (!endSlot) {
                    isSelected = (t === startSlot);
                } else {
                    isSelected = (t >= startSlot && t <= endSlot);
                }

                if (isSelected) {
                    blocks++;
                    var inp = document.createElement('input');
                    inp.type = 'hidden'; inp.name = 'slots[]'; inp.value = t;
                    hiddenContainer.appendChild(inp);
                }
            }
        }
    }

    const nameInput = document.getElementById('customerSearchInput');
    const hasName = nameInput && nameInput.value.trim() !== '';

    if (blocks < 2 || !hasName) {
        if(btnSubmit) btnSubmit.classList.add('disabled');
        // We still calculate services!
    } else {
        if(btnSubmit) btnSubmit.classList.remove('disabled');
    }

    const selectedSlots = Array.from(hiddenContainer.querySelectorAll('input')).map(i => i.value);

    if (blocks >= 2) {
        if (bookingType === 'fixed') {
            validDates.forEach(dStr => {
                const dateObj = new Date(dStr);
                const dayW = dateObj.getDay();
                selectedSlots.forEach(time => {
                    let mult = 1.0;
                    const parts = time.split(':');
                    const mins = parseInt(parts[0]) * 60 + parseInt(parts[1]);
                    if (mins >= 1050 && mins < 1290) mult = 1.5;
                    if (dayW === 0 || dayW === 6) mult = 1.25;
                    totalBase += 0.5 * basePriceHour * mult;
                });
            });
        } else {
            const dateObj = new Date(singleDateStr);
            const dayW = dateObj.getDay();
            selectedSlots.forEach(time => {
                let mult = 1.0;
                const parts = time.split(':');
                const mins = parseInt(parts[0]) * 60 + parseInt(parts[1]);
                if (mins >= 1050 && mins < 1290) mult = 1.5;
                if (dayW === 0 || dayW === 6) mult = 1.25;
                totalBase += 0.5 * basePriceHour * mult;
            });
        }
    }

    let svcPerMatch = 0;
    document.querySelectorAll('.service-item').forEach(item => {
        const chk = item.querySelector('.service-checkbox');
        const qty = item.querySelector('.service-qty');
        
        if (chk && chk.value === 'tea') {
            const labelSpan = item.querySelector('.service-label-text');
            if (bookingType === 'fixed' && matchesCount >= 3) {
                chk.dataset.price = "0";
                if (labelSpan) labelSpan.innerHTML = 'Trà đá <span class="badge bg-success ms-1">Miễn phí 🎁</span>';
            } else {
                chk.dataset.price = "{{ \App\Models\Setting::get('tea_price', 10000) }}";
                if (labelSpan) labelSpan.innerHTML = 'Trà đá (ca)';
            }
        }

        if (chk && chk.checked) {
            svcPerMatch += parseInt(chk.dataset.price || 0) * parseInt(qty.value || 1);
        }
    });
    
    const totalSvc = svcPerMatch * matchesCount;
    const total = totalBase + totalSvc;

    if (blocks === 1) {
        if(baseCostEl) baseCostEl.innerHTML = '<span class="text-danger" style="font-size:0.75rem;">(Tối thiểu 1 tiếng)</span>';
    } else {
        if(baseCostEl) baseCostEl.innerHTML = fmt(totalBase) + (matchesCount > 1 && totalBase > 0 ? ` <span class="text-muted small">(${matchesCount} trận)</span>` : '');
    }

    if(serviceCostEl) serviceCostEl.innerHTML = fmt(totalSvc) + (matchesCount > 1 && svcPerMatch > 0 ? ` <span class="text-muted small">(${matchesCount} trận)</span>` : '');
    if(totalCostEl) totalCostEl.textContent = fmt(total);
    const mt = document.getElementById('modalTotalAmount');
    if(mt) mt.textContent = fmt(total);
    if(totalInput) totalInput.value = total;
}

function initPOS() {
    if(!syncData()) return;

    const form = document.getElementById('posForm');
    const pitchSelect = document.getElementById('posPitchSelect');
    const dateSelect = document.getElementById('posDateSelect');
    const monthSelect = document.getElementById('posMonthSelect');
    const bookingTypeSelect = document.getElementById('posBookingType');
    const totalInput = document.getElementById('posTotalInput');

    // Prevent attaching multiple times if turbo:load fires again without full reload
    if(window._posInitialized) return;
    window._posInitialized = true;

    function reloadPage() {
        const type = bookingTypeSelect.value;
        const pId = pitchSelect.value;
        let shouldReload = false;
        if (type === 'single') { if(pId && dateSelect.value) shouldReload = true; }
        else { if(pId && monthSelect.value) shouldReload = true; }
        
        if(shouldReload) {
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            params.delete('_token');
            if (type === 'single') {
                params.delete('booking_month');
                params.delete('days_of_week[]');
            } else {
                params.delete('booking_date');
            }
            const qs = params.toString();
            const url = '?' + qs;
            window.history.replaceState(null, '', url);

            const dyn = document.getElementById('dynamic-content');
            if (dyn) dyn.style.opacity = '0.5';

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newDyn = doc.getElementById('dynamic-content');
                    if(newDyn && dyn) dyn.innerHTML = newDyn.innerHTML;
                    
                    const newStore = doc.getElementById('pos-data-store');
                    const oldStore = document.getElementById('pos-data-store');
                    if(newStore && oldStore) oldStore.replaceWith(newStore);
                    
                    if (dyn) dyn.style.opacity = '1';
                    
                    syncData();
                    startSlot = null; endSlot = null;
                    bindServiceEvents();
                    renderSlots();
                    calc();
                });
        }
    }

    bookingTypeSelect.addEventListener('change', function() {
        const type = this.value;
        const colSingleDate = document.getElementById('colSingleDate');
        const colFixedMonth = document.getElementById('colFixedMonth');
        const colFixedDays = document.getElementById('colFixedDays');
        
        if (type === 'single') {
            colSingleDate.classList.remove('d-none');
            colFixedMonth.classList.add('d-none');
            colFixedDays.classList.add('d-none');
            dateSelect.required = true;
            monthSelect.required = false;
        } else {
            colSingleDate.classList.add('d-none');
            colFixedMonth.classList.remove('d-none');
            colFixedDays.classList.remove('d-none');
            dateSelect.required = false;
            monthSelect.required = true;
        }
        reloadPage();
    });

    pitchSelect.addEventListener('change', reloadPage);
    dateSelect.addEventListener('change', reloadPage);
    monthSelect.addEventListener('change', reloadPage);
    
    document.body.addEventListener('change', function(e) {
        if(e.target && e.target.classList.contains('pos-day-checkbox')) {
            reloadPage();
        }
    });

    const nameInput = document.getElementById('customerSearchInput');
    if (nameInput && !nameInput.dataset.boundPos) {
        nameInput.dataset.boundPos = '1';
        nameInput.addEventListener('input', calc);
    }

    window.generateQR = function() {
        const amount = totalInput.value;
        if(amount <= 0) return;
        const img = document.getElementById('qrImage');
        const loading = document.getElementById('qrLoading');
        
        const rawName = document.getElementById('customerSearchInput').value;
        const safeName = rawName.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
        const addInfo = 'POS' + safeName;

        if(img && loading) {
            img.classList.add('d-none'); loading.classList.remove('d-none');
            img.src = `https://img.vietqr.io/image/970422-0359858362-compact2.jpg?amount=${amount}&addInfo=${addInfo}`;
            img.onload = () => { loading.classList.add('d-none'); img.classList.remove('d-none'); };
        }
    };

    window.openPaymentModal = function() {
        const form = document.getElementById('posForm');
        // Trigger HTML5 validation (shows tooltip on empty required fields)
        if (typeof form.reportValidity === 'function') {
            if (!form.reportValidity()) return;
        } else {
            const nameInput = document.getElementById('customerSearchInput');
            if (!nameInput.value.trim()) {
                nameInput.focus();
                return;
            }
        }
        
        // Form is valid, open modal
        const modalEl = document.getElementById('paymentModal');
        let modal = bootstrap.Modal.getInstance(modalEl);
        if (!modal) {
            modal = new bootstrap.Modal(modalEl);
        }
        modal.show();
    };

    bindServiceEvents();
    renderSlots();
    calc();
    initAutocomplete();
    toggleEmailRequirement();
}

document.addEventListener("turbo:load", function() {
    window._posInitialized = false;
    startSlot = null;
    endSlot = null;
    initPOS();
});
</script>
@endpush
@endsection
