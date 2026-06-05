@extends('layouts.admin')
@section('title', 'Thanh toán — FieldBook Admin')
@section('content')
<h1 class="h4 fw-bold">Quản lý thanh toán</h1>
<p class="text-muted small">Theo dõi tất cả giao dịch thanh toán trong hệ thống.</p>

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
<div class="card-fb p-4 mt-4">
    <form method="GET" action="{{ route('admin.payments.index') }}" class="d-flex flex-wrap gap-3 align-items-end">
        <div>
            <label class="form-label small text-muted mb-1">Trạng thái</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
        </div>
        <div>
            <label class="form-label small text-muted mb-1">Phương thức</label>
            <select name="method" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                <option value="transfer" {{ request('method') === 'transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                <option value="momo" {{ request('method') === 'momo' ? 'selected' : '' }}>MoMo</option>
            </select>
        </div>
        <div>
            <label class="form-label small text-muted mb-1">Từ ngày</label>
            <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
        </div>
        <div>
            <label class="form-label small text-muted mb-1">Đến ngày</label>
            <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
        </div>
        <div class="d-flex align-items-end">
            <button type="submit" class="btn btn-sm btn-primary px-3" style="background:var(--fb-primary);border-color:var(--fb-primary);">
                <i class="bi bi-funnel me-1"></i> Lọc dữ liệu
            </button>
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
                </tr>
                @empty
                <tr><td colspan="7" class="text-muted text-center py-4">Không có giao dịch nào</td></tr>
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
@endif
@endsection
