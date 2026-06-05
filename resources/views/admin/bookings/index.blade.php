@extends('layouts.admin')
@section('title', 'Quản lý booking — FieldBook Admin')
@section('content')
<h1 class="h4 fw-bold">Quản lý booking</h1>
<p class="text-muted small">Tất cả booking trên hệ thống.</p>

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Filters --}}
<div class="card-fb p-4 mt-3">
    <form method="GET" action="{{ route('admin.bookings.index') }}" class="d-flex flex-wrap gap-3 align-items-end">
        <div>
            <label class="form-label small text-muted mb-1">Trạng thái</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div>
            <label class="form-label small text-muted mb-1">Loại</label>
            <select name="type" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                <option value="hourly" {{ request('type') === 'hourly' ? 'selected' : '' }}>Theo giờ</option>
                <option value="monthly" {{ request('type') === 'monthly' ? 'selected' : '' }}>Tháng cố định</option>
            </select>
        </div>
        <div>
            <label class="form-label small text-muted mb-1">Sân</label>
            <select name="pitch_id" class="form-select form-select-sm">
                <option value="">Tất cả</option>
                @foreach($pitches as $pitch)
                <option value="{{ $pitch->id }}" {{ request('pitch_id') == $pitch->id ? 'selected' : '' }}>{{ $pitch->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small text-muted mb-1">Ngày</label>
            <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
        </div>
        <div class="d-flex align-items-end">
            <button type="submit" class="btn btn-sm btn-primary px-3" style="background:var(--fb-primary);border-color:var(--fb-primary);">
                <i class="bi bi-funnel me-1"></i> Lọc dữ liệu
            </button>
        </div>
    </form>
</div>

{{-- Bookings Table --}}
<div class="card-fb mt-4">
    <div class="table-responsive">
        <table class="table table-hover mb-0 small">
            <thead class="text-uppercase text-muted" style="font-size:0.7rem; background:rgba(0,0,0,0.02);">
                <tr>
                    <th class="px-4 py-3">Mã đơn</th>
                    <th class="px-4 py-3">Khách hàng</th>
                    <th class="px-4 py-3">Sân</th>
                    <th class="px-4 py-3">Ngày</th>
                    <th class="px-4 py-3">Giờ</th>
                    <th class="px-4 py-3">Loại</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3 text-end">Tổng tiền</th>
                    <th class="px-4 py-3 text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                <tr>
                    <td class="px-4 py-3 font-monospace" style="font-size:0.75rem;">FB-{{ str_pad($b->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-4 py-3 fw-medium">{{ $b->user->name }}</td>
                    <td class="px-4 py-3">{{ $b->pitch->name }}</td>
                    <td class="px-4 py-3">{{ $b->booking_date?->format('d/m') }}</td>
                    <td class="px-4 py-3">{{ $b->start_time }}–{{ $b->end_time }}</td>
                    <td class="px-4 py-3">
                        <span class="badge-status {{ $b->type === 'monthly' ? 'status-monthly' : 'status-hourly' }}">
                            {{ $b->type === 'monthly' ? 'Tháng' : 'Giờ' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($b->status === 'confirmed')
                            <span class="badge-status status-confirmed">Confirmed</span>
                        @elseif($b->status === 'pending')
                            <span class="badge-status status-pending">Pending</span>
                        @elseif($b->status === 'cancelled')
                            <span class="badge-status status-cancelled">Cancelled</span>
                        @else
                            <span class="badge-status status-cancelled">{{ ucfirst($b->status) }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-end fw-semibold">{{ number_format($b->total_amount) }}đ</td>
                    <td class="px-4 py-3">
                        @if($b->status === 'pending')
                        <div class="d-flex justify-content-end gap-1">
                            <form action="{{ route('admin.bookings.confirm', $b) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-success d-flex align-items-center gap-1">
                                    <i class="bi bi-check-lg"></i> Xác nhận
                                </button>
                            </form>
                            <form action="{{ route('admin.bookings.cancel', $b) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Bạn chắc chắn muốn hủy booking này?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
                                    <i class="bi bi-x-lg"></i> Hủy
                                </button>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-muted text-center py-4">Không có booking nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($bookings->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $bookings->withQueryString()->links() }}
</div>
@endif
@endsection
