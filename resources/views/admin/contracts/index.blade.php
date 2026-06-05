@extends('layouts.admin')
@section('title', 'Hợp đồng tháng — FieldBook Admin')
@section('content')
<h1 class="h4 fw-bold">Hợp đồng tháng &amp; Lock</h1>
<p class="text-muted small">Quản lý hợp đồng cố định và slot đang khóa.</p>

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Filters --}}
<div class="card-fb p-4 mt-3">
    <form method="GET" action="{{ route('admin.contracts.index') }}" class="d-flex flex-wrap gap-3 align-items-end">
        <div>
            <label class="form-label small text-muted mb-1">Trạng thái HĐ</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="renewed" {{ request('status') === 'renewed' ? 'selected' : '' }}>Renewed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div>
            <label class="form-label small text-muted mb-1">Lock Status</label>
            <select name="lock_status" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="active" {{ request('lock_status') === 'active' ? 'selected' : '' }}>Đang khóa</option>
                <option value="expired" {{ request('lock_status') === 'expired' ? 'selected' : '' }}>Hết hạn khóa</option>
            </select>
        </div>
        <div class="d-flex align-items-end">
            <button type="submit" class="btn btn-sm btn-primary px-3" style="background:var(--fb-primary);border-color:var(--fb-primary);">
                <i class="bi bi-funnel me-1"></i> Lọc dữ liệu
            </button>
        </div>
    </form>
</div>

{{-- Contracts Table --}}
<div class="card-fb mt-4">
    <div class="table-responsive">
        <table class="table table-hover mb-0 small">
            <thead class="text-uppercase text-muted" style="font-size:0.7rem; background:rgba(0,0,0,0.02);">
                <tr>
                    <th class="px-4 py-3">Mã HĐ</th>
                    <th class="px-4 py-3">Khách hàng</th>
                    <th class="px-4 py-3">Sân</th>
                    <th class="px-4 py-3">Tháng</th>
                    <th class="px-4 py-3">Thứ/Giờ</th>
                    <th class="px-4 py-3">Trạng thái HĐ</th>
                    <th class="px-4 py-3">Lock</th>
                    <th class="px-4 py-3 text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $c)
                <tr>
                    <td class="px-4 py-3 font-monospace" style="font-size:0.75rem;">CT-{{ str_pad($c->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-4 py-3 fw-medium">{{ $c->user->name }}</td>
                    <td class="px-4 py-3">{{ $c->pitch->name }}</td>
                    <td class="px-4 py-3">{{ $c->month ? \Carbon\Carbon::parse($c->month)->format('m/Y') : '—' }}</td>
                    <td class="px-4 py-3">{{ $c->day_of_week }} {{ $c->start_time }}–{{ $c->end_time }}</td>
                    <td class="px-4 py-3">
                        @if($c->status === 'active')
                            <span class="badge-status status-confirmed">Active</span>
                        @elseif($c->status === 'renewed')
                            <span class="badge-status status-monthly">Renewed</span>
                        @elseif($c->status === 'cancelled')
                            <span class="badge-status status-cancelled">Cancelled</span>
                        @else
                            <span class="badge-status status-cancelled">{{ ucfirst($c->status) }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($c->lock_status === 'active')
                            <span class="badge-status status-pending">🔒 Đang giữ chỗ</span>
                        @else
                            <span class="badge-status status-cancelled">Hết hạn khóa</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-end">
                        @if($c->lock_status === 'active')
                        <form action="{{ route('admin.contracts.release', $c) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Bạn chắc chắn muốn giải phóng lock cho hợp đồng CT-{{ str_pad($c->id, 4, '0', STR_PAD_LEFT) }}?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-warning d-flex align-items-center gap-1">
                                <i class="bi bi-unlock"></i> Giải phóng Lock
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-muted text-center py-4">Không có hợp đồng nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($contracts->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $contracts->withQueryString()->links() }}
</div>
@endif
@endsection
