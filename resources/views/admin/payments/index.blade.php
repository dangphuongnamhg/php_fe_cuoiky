@extends('layouts.admin')
@section('title', 'Thanh toán — SanGo Admin')
@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Quản lý thanh toán</h1>
    </div>
</div>

{{-- KPI Card --}}
<div class="row g-3 mt-2">
    <div class="col-sm-6 col-lg-4">
        <div class="card-fb p-4 h-100 d-flex flex-column justify-content-center">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(25,135,84,0.12);color:#198754;">
                    <i class="bi bi-wallet2 fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium text-uppercase" style="letter-spacing:0.05em;font-size:0.7rem;">Doanh thu (Completed)</div>
                    <div class="fs-4 fw-bold text-success">{{ number_format($totalCompleted) }}đ</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card-fb p-4 h-100 d-flex flex-column justify-content-center">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(243,156,18,0.15);color:#856404;">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium text-uppercase" style="letter-spacing:0.05em;font-size:0.7rem;">Đang chờ xử lý</div>
                    <div class="fs-4 fw-bold text-warning">{{ number_format($totalPending) }}đ</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card-fb p-4 h-100 d-flex flex-column justify-content-center">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(220,53,69,0.1);color:#dc3545;">
                    <i class="bi bi-arrow-counterclockwise fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium text-uppercase" style="letter-spacing:0.05em;font-size:0.7rem;">Đã hoàn tiền</div>
                    <div class="fs-4 fw-bold text-danger">{{ number_format($totalRefunded) }}đ</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card-fb p-3 mb-4" style="overflow: visible !important;">
    <form method="GET" action="{{ route('admin.payments.index') }}" class="d-flex flex-wrap gap-3 align-items-end" id="searchForm" data-turbo="false">
        <div class="flex-grow-1 position-relative" style="max-width: 250px;">
            <label class="form-label small text-muted mb-1">Tìm kiếm</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="search" id="paymentSearchInput" class="form-control border-start-0 ps-0" placeholder="Mã TT, Tên, Email..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <ul class="dropdown-menu w-100 shadow-sm" id="paymentSearchDropdown" style="display:none; position:absolute; top:100%; left:0; z-index:1050; max-height:250px; overflow-y:auto; border-radius:0.5rem; margin-top:4px;">
            </ul>
        </div>
        <div>
            <label class="form-label small text-muted mb-1">Trạng thái</label>
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
        </div>
        <div>
            <label class="form-label small text-muted mb-1">Phương thức</label>
            <select name="method" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả</option>
                <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                <option value="transfer" {{ request('method') === 'transfer' ? 'selected' : '' }}>Chuyển khoản</option>
            </select>
        </div>
        <div>
            <label class="form-label small text-muted mb-1">Từ ngày</label>
            <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}" onchange="this.form.submit()">
        </div>
        <div>
            <label class="form-label small text-muted mb-1">Đến ngày</label>
            <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}" onchange="this.form.submit()">
        </div>
        <div class="d-flex align-items-end gap-2">
            <button type="submit" class="d-none">Lọc</button>
            @if(request()->hasAny(['status', 'method', 'from_date', 'to_date']) && (request('status') || request('method') || request('from_date') || request('to_date')))
                <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-light text-danger">Bỏ lọc</a>
            @endif
        </div>
    </form>
</div>

{{-- Payments Table --}}
<div class="card-fb mt-4">
    <div class="table-responsive">
        <table class="table table-hover mb-0 small">
            <thead class="text-uppercase text-muted" style="font-size:0.7rem; background:rgba(0,0,0,0.02);">
                <tr>
                    <th class="px-4 py-3">Mã TT</th>
                    <th class="px-4 py-3">Booking</th>
                    <th class="px-4 py-3">Khách hàng</th>
                    <th class="px-4 py-3 text-end">Số tiền</th>
                    <th class="px-4 py-3">Phương thức</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3">Thời gian</th>
                    <th class="px-4 py-3 text-center">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $pay)
                <tr>
                    <td class="px-4 py-3 font-monospace" style="font-size:0.75rem;">PAY-{{ str_pad($pay->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-4 py-3">
                        <div class="d-flex flex-column">
                            <span class="fw-medium font-monospace" style="font-size:0.75rem;">FB-{{ str_pad($pay->booking->id ?? 0, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-muted" style="font-size:0.7rem;">{{ $pay->booking->pitch->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle" style="width:28px;height:28px;font-size:0.65rem;">{{ mb_substr($pay->booking->user->name ?? '?', -2, 1) }}</div>
                            <span class="fw-medium">{{ $pay->booking->user->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-end fw-semibold">{{ number_format($pay->amount) }}đ</td>
                    <td class="px-4 py-3">
                        @if($pay->method === 'cash')
                            <span class="badge-status status-confirmed"><i class="bi bi-cash-stack me-1"></i>Tiền mặt</span>
                        @elseif($pay->method === 'transfer')
                            <span class="badge-status status-monthly"><i class="bi bi-bank me-1"></i>Chuyển khoản</span>
                        @elseif($pay->method === 'momo')
                            <span class="badge-status" style="background:#fce4ec;color:#c2185b;"><i class="bi bi-phone me-1"></i>MoMo</span>
                        @else
                            <span class="badge-status status-cancelled">{{ ucfirst($pay->method) }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($pay->status === 'completed')
                            <span class="badge-status status-confirmed">Completed</span>
                        @elseif($pay->status === 'pending')
                            <span class="badge-status status-pending">Pending</span>
                        @elseif($pay->status === 'refunded')
                            <span class="badge-status status-cancelled" style="color:#dc3545;">Refunded</span>
                        @else
                            <span class="badge-status status-cancelled">{{ ucfirst($pay->status) }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-muted">
                        {{ $pay->paid_at ? $pay->paid_at->format('d/m/Y H:i') : ($pay->created_at?->format('d/m/Y H:i') ?? '—') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" class="btn btn-sm btn-light text-primary border-0 shadow-sm" style="width:32px;height:32px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;" data-bs-toggle="modal" data-bs-target="#paymentDetailModal-{{ $pay->id }}" title="Xem chi tiết">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-muted text-center py-4">Không có giao dịch nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($payments->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $payments->withQueryString()->links() }}
</div>
</div>
@endif

{{-- Detail Modals --}}
@foreach($payments as $pay)
<div class="modal fade" id="paymentDetailModal-{{ $pay->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Chi tiết thanh toán</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fs-5 fw-bold font-monospace">PAY-{{ str_pad($pay->id, 5, '0', STR_PAD_LEFT) }}</span>
                    @if($pay->status === 'completed')
                        <span class="badge-status status-confirmed">Completed</span>
                    @elseif($pay->status === 'pending')
                        <span class="badge-status status-pending">Pending</span>
                    @elseif($pay->status === 'refunded')
                        <span class="badge-status status-cancelled">Refunded</span>
                    @else
                        <span class="badge-status status-cancelled">{{ ucfirst($pay->status) }}</span>
                    @endif
                </div>

                <div class="mb-4">
                    <div class="text-muted small fw-medium mb-1">Khách hàng</div>
                    <div class="fw-semibold">{{ $pay->booking->user->name ?? '—' }}</div>
                    @if($pay->booking && $pay->booking->user)
                    <div class="small text-muted">{{ $pay->booking->user->email }}</div>
                    @endif
                </div>

                <div class="row mb-4">
                    <div class="col-6">
                        <div class="text-muted small fw-medium mb-1">Mã Đặt sân</div>
                        <div class="fw-medium font-monospace">FB-{{ str_pad($pay->booking->id ?? 0, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small fw-medium mb-1">Sân</div>
                        <div class="fw-medium">{{ $pay->booking->pitch->name ?? '—' }}</div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-6">
                        <div class="text-muted small fw-medium mb-1">Phương thức</div>
                        <div class="fw-medium">
                            @if($pay->method === 'cash')
                                <i class="bi bi-cash-stack me-1 text-success"></i> Tiền mặt
                            @elseif($pay->method === 'transfer')
                                <i class="bi bi-bank me-1 text-primary"></i> Chuyển khoản
                            @elseif($pay->method === 'momo')
                                <i class="bi bi-phone me-1 text-danger"></i> MoMo
                            @else
                                {{ ucfirst($pay->method) }}
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small fw-medium mb-1">Thời gian</div>
                        <div class="small">{{ $pay->paid_at ? $pay->paid_at->format('d/m/Y H:i') : ($pay->created_at?->format('d/m/Y H:i') ?? '—') }}</div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                    <span class="fw-medium">Tổng tiền</span>
                    <span class="fs-4 fw-bold text-success">{{ number_format($pay->amount) }}đ</span>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0 py-3">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
document.addEventListener('turbo:load', function() {
    const searchInput = document.getElementById('paymentSearchInput');
    const dropdown = document.getElementById('paymentSearchDropdown');
    
    if (!searchInput || !dropdown) return;
    
    const allPayments = @json($allPaymentsForSearch ?? []);

    function removeAccents(str) {
        if (!str) return '';
        return String(str).normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    }

    function renderDropdown(val) {
        dropdown.innerHTML = '';
        
        if (val.length === 0) {
            dropdown.classList.remove('show');
            dropdown.style.display = 'none';
            return;
        }

        const matches = allPayments.filter(p => {
            const code = 'pay-' + String(p.id).padStart(5, '0');
            const name = p.booking && p.booking.user ? p.booking.user.name : '';
            const email = p.booking && p.booking.user ? p.booking.user.email : '';
            
            return removeAccents(code).includes(val) || 
                   removeAccents(name).includes(val) || 
                   removeAccents(email).includes(val);
        }).slice(0, 8);

        if (matches.length > 0) {
            matches.forEach(match => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.className = 'dropdown-item small py-2 text-wrap cursor-pointer d-flex flex-column';
                a.href = 'javascript:void(0)';
                
                const code = 'PAY-' + String(match.id).padStart(5, '0');
                const name = match.booking && match.booking.user ? match.booking.user.name : 'Khách';
                
                a.innerHTML = `<span class="fw-bold text-primary">${code}</span><span class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-person me-1"></i>${name}</span>`;
                
                a.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    searchInput.value = code;
                    dropdown.classList.remove('show');
                    dropdown.style.display = 'none';
                    document.getElementById('searchForm').submit();
                });
                
                li.appendChild(a);
                dropdown.appendChild(li);
            });
            dropdown.classList.add('show');
            dropdown.style.display = 'block';
        } else {
            dropdown.classList.remove('show');
            dropdown.style.display = 'none';
        }
    }

    searchInput.addEventListener('input', function() {
        const val = removeAccents(this.value.trim());
        renderDropdown(val);
    });

    document.addEventListener('mousedown', function(e) {
        if (dropdown.classList.contains('show') && !searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
            dropdown.style.display = 'none';
        }
    });
    
    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length > 0) {
            renderDropdown(removeAccents(this.value.trim()));
        }
    });
});
</script>

@endsection
