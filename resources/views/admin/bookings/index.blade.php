@extends('layouts.admin')
@section('title', 'Quản lý booking — SanGo Admin')
@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Quản lý booking</h1>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<!-- Global SweetAlert handles this -->
@endif

{{-- 3 Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="{{ route('admin.bookings.index', ['date' => date('Y-m-d')]) }}" class="text-decoration-none">
            <div class="card-fb p-3 d-flex align-items-center gap-3" style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width:48px;height:48px;background:rgba(13,110,253,0.1);">
                    <i class="bi bi-receipt fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Tổng Đơn Hôm Nay</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($todayBookings) }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="text-decoration-none">
            <div class="card-fb p-3 d-flex align-items-center gap-3" style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-warning" style="width:48px;height:48px;background:rgba(255,193,7,0.1);">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Đơn Chờ Duyệt</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($pendingBookings) }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <div class="card-fb p-3 d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-success" style="width:48px;height:48px;background:rgba(25,135,84,0.1);">
                <i class="bi bi-cash-stack fs-4"></i>
            </div>
            <div>
                <div class="text-muted small fw-medium">Doanh Thu Trong Ngày</div>
                <div class="fs-4 fw-bold text-dark">{{ number_format($todayRevenue) }}đ</div>
            </div>
        </div>
    </div>
</div>

{{-- Filters and Search Bar --}}
<div class="card-fb mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="row g-2 align-items-center">
            <!-- Search -->
            <div class="col-12 col-md-3">
                <div class="input-group">
                    <button type="submit" class="input-group-text bg-light border-0 text-muted ps-3 rounded-start-pill"><i class="bi bi-search"></i></button>
                    <input type="text" name="search" class="form-control bg-light border-0 shadow-none rounded-end-pill" placeholder="Mã đơn, Tên KH..." value="{{ request('search') }}">
                </div>
            </div>
            <!-- Filter Type -->
            <div class="col-6 col-md-2">
                <select name="type" class="form-select bg-light border-0 shadow-none rounded-pill text-muted px-3" onchange="this.form.submit()">
                    <option value="">Tất cả Loại</option>
                    <option value="hourly" {{ request('type') === 'hourly' ? 'selected' : '' }}>Theo giờ</option>
                    <option value="monthly" {{ request('type') === 'monthly' ? 'selected' : '' }}>Theo tháng</option>
                </select>
            </div>
            <!-- Filter Status -->
            <div class="col-6 col-md-2">
                <select name="status" class="form-select bg-light border-0 shadow-none rounded-pill text-muted px-3" onchange="this.form.submit()">
                    <option value="">Tất cả Trạng thái</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <!-- Filter Pitch -->
            <div class="col-6 col-md-2">
                <select name="pitch_id" class="form-select bg-light border-0 shadow-none rounded-pill text-muted px-3" onchange="this.form.submit()">
                    <option value="">Tất cả Sân</option>
                    @foreach($pitches as $pitch)
                    <option value="{{ $pitch->id }}" {{ request('pitch_id') == $pitch->id ? 'selected' : '' }}>{{ $pitch->name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Filter Date -->
            <div class="col-6 col-md-2">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 text-muted ps-3 rounded-start-pill" title="Lọc theo ngày">
                        <i class="bi bi-calendar-date"></i>
                    </span>
                    <input type="date" name="date" class="form-control bg-light border-0 shadow-none rounded-end-pill text-muted px-2" value="{{ request('date') }}" onchange="this.form.submit()">
                </div>
            </div>
            <!-- Actions -->
            <div class="col-12 col-md-1 text-md-end text-center mt-2 mt-md-0">
                @if(request()->hasAny(['search', 'type', 'status', 'pitch_id', 'date']) && array_filter(request()->only(['search', 'type', 'status', 'pitch_id', 'date'])))
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-light text-danger rounded-pill" title="Xóa bộ lọc">
                    <i class="bi bi-x-circle"></i> Bỏ lọc
                </a>
                @endif
            </div>
        </form>
    </div>
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
                    <th class="px-4 py-3">Ngày & Giờ</th>
                    <th class="px-4 py-3">Thanh toán</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3 text-end">Tổng tiền</th>
                    <th class="px-4 py-3 text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                <tr>
                    <td class="px-4 py-3">
                        <div class="fw-bold text-primary" style="font-size:0.85rem;">FB-{{ str_pad($b->id, 4, '0', STR_PAD_LEFT) }}</div>
                        @if($b->notes)
                        <div class="text-muted small text-truncate mt-1" style="max-width:150px;" title="{{ $b->notes }}">
                            {{ str_replace('[POS Cố định] Khách: ', '', str_replace('[POS] Khách: ', '', $b->notes)) }}
                        </div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $actualName = $b->user->name;
                            if ($b->user->email === 'pos@fieldbook.local' && $b->notes) {
                                $actualName = str_replace(['[POS Cố định] Khách: ', '[POS] Khách: '], '', $b->notes);
                            }
                        @endphp
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle flex-shrink-0" style="width:32px;height:32px;background:var(--fb-secondary);font-size:0.8rem;">
                                {{ mb_substr($actualName, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-medium text-dark lh-1 mb-1">{{ $actualName }}</div>
                                @if($b->user->email !== 'pos@fieldbook.local')
                                <div class="text-muted" style="font-size:0.7rem;">{{ $b->user->email }}</div>
                                @else
                                <div class="text-muted" style="font-size:0.7rem;">Khách vãng lai</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="fw-medium">{{ $b->pitch->name }}</div>
                        <div class="mt-1">
                            <span class="badge-status {{ $b->booking_type === 'monthly' ? 'status-monthly' : 'status-hourly' }}" style="font-size:0.65rem; padding: 0.15rem 0.4rem;">
                                {{ $b->booking_type === 'monthly' ? 'Tháng' : 'Giờ' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="fw-medium"><i class="bi bi-calendar3 me-1 text-muted"></i>{{ $b->booking_date?->format('d/m/Y') }}</div>
                        <div class="text-muted small mt-1"><i class="bi bi-clock me-1 text-muted"></i>{{ $b->start_time }} – {{ $b->end_time }}</div>
                    </td>
                    <td class="px-4 py-3">
                        @if($b->payment)
                            @php
                                $pm = $b->payment->method;
                                $pmText = $pm === 'cash' ? 'Tiền mặt' : ($pm === 'transfer' ? 'Chuyển khoản' : ($pm === 'momo' ? 'MoMo' : ucfirst($pm)));
                            @endphp
                            <div class="small fw-medium">{{ $pmText }}</div>
                            <div class="small mt-1 text-{{ $b->payment->status === 'completed' ? 'success' : 'warning' }}">
                                {{ $b->payment->status === 'completed' ? 'Đã thanh toán' : 'Chờ thanh toán' }}
                            </div>
                        @else
                            <span class="text-muted small">Chưa có</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($b->status === 'confirmed')
                            <span class="badge-status status-confirmed">Đã xác nhận</span>
                        @elseif($b->status === 'pending')
                            <span class="badge-status status-pending">Chờ duyệt</span>
                        @elseif($b->status === 'cancelled')
                            <span class="badge-status status-cancelled">Đã hủy</span>
                        @else
                            <span class="badge-status status-cancelled">{{ ucfirst($b->status) }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-end fw-bold text-dark">{{ number_format($b->total_price) }}đ</td>
                    <td class="px-4 py-3">
                        <div class="d-flex justify-content-end gap-1">
                            <button type="button" class="btn btn-sm btn-light text-primary rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#detailBookingModal-{{ $b->id }}" title="Xem chi tiết">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                            @if($b->status === 'pending')
                            <form action="{{ route('admin.bookings.confirm', $b) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-success d-flex align-items-center gap-1 py-1 px-2" title="Xác nhận">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 py-1 px-2" data-bs-toggle="modal" data-bs-target="#cancelBookingModal-{{ $b->id }}" title="Hủy">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-muted text-center py-4">Không có booking nào phù hợp với bộ lọc</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
    <div class="card-footer bg-white border-top-0 py-3 d-flex justify-content-center">
        {{ $bookings->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<style>
/* Custom Pagination Styles */
.pagination {
    margin-bottom: 0;
    gap: 0.3rem;
}
.page-item .page-link {
    border-radius: 8px !important;
    min-width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 10px;
    color: #475569;
    border: 1px solid transparent;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.2s ease;
    background-color: #f8fafc;
}
.page-item .page-link:hover {
    background-color: #e2e8f0;
    color: var(--fb-primary);
}
.page-item.active .page-link {
    background-color: var(--fb-primary) !important;
    color: #fff !important;
    border-color: var(--fb-primary) !important;
    box-shadow: 0 4px 10px rgba(31,78,121, 0.25);
}
.page-item.disabled .page-link {
    color: #cbd5e1;
    background-color: transparent;
}
/* Hide the text from Laravel default bootstrap pagination */
nav > div.d-sm-flex > div:first-child {
    display: none !important;
}
nav > div.d-sm-flex {
    justify-content: center !important;
}
nav > div.d-sm-flex > div:last-child {
    margin: 0 auto;
}
</style>

{{-- Detail Modals --}}
@foreach($bookings as $b)
<div class="modal fade" id="detailBookingModal-{{ $b->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-bottom-0" style="background: linear-gradient(135deg, rgba(13,110,253,0.05), rgba(13,110,253,0.1));">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                        <i class="bi bi-receipt fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Chi tiết đơn hàng</h5>
                        <div class="text-primary small fw-semibold">FB-{{ str_pad($b->id, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="bg-light rounded-4 p-3 mb-4 border" style="border-color: rgba(0,0,0,0.05) !important;">
                    @php
                        $modalActualName = $b->user->name;
                        if ($b->user->email === 'pos@fieldbook.local' && $b->notes) {
                            $modalActualName = str_replace(['[POS Cố định] Khách: ', '[POS] Khách: '], '', $b->notes);
                        }
                    @endphp
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-circle flex-shrink-0" style="width:48px;height:48px;background:var(--fb-secondary);font-size:1.2rem;">
                            {{ mb_substr($modalActualName, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-muted small fw-medium mb-1">Khách hàng</div>
                            <div class="fw-bold text-dark fs-5 lh-1">{{ $modalActualName }}</div>
                            @if($b->user->email && $b->user->email !== 'pos@fieldbook.local')
                            <div class="text-muted small mt-1"><i class="bi bi-envelope me-1"></i>{{ $b->user->email }}</div>
                            @else
                            <div class="text-muted small mt-1"><i class="bi bi-person-badge me-1"></i>Khách vãng lai (POS)</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted small"><i class="bi bi-geo-alt me-2"></i>Sân</span>
                        <span class="fw-bold text-dark text-end">{{ $b->pitch->name }} <span class="badge bg-secondary ms-1">{{ $b->booking_type === 'monthly' ? 'Tháng' : 'Giờ' }}</span></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted small"><i class="bi bi-calendar3 me-2"></i>Ngày & Giờ</span>
                        <span class="fw-bold text-dark text-end">{{ $b->booking_date?->format('d/m/Y') }} <span class="text-muted mx-1">|</span> <span class="text-primary">{{ $b->start_time }} – {{ $b->end_time }}</span></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted small"><i class="bi bi-credit-card me-2"></i>Thanh toán</span>
                        <span class="fw-bold text-dark text-end d-flex align-items-center gap-2">
                            @if($b->payment)
                                {{ $b->payment->method === 'cash' ? 'Tiền mặt' : ($b->payment->method === 'transfer' ? 'Chuyển khoản' : ($b->payment->method === 'momo' ? 'MoMo' : ucfirst($b->payment->method))) }}
                                @if($b->payment->status === 'completed')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>Đã thanh toán</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1"><i class="bi bi-clock-history me-1"></i>Chờ xử lý</span>
                                @endif
                            @else
                                <span class="text-muted">Chưa có</span>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="bg-primary bg-opacity-10 rounded-4 p-3 mt-4 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-primary">Tổng tiền thanh toán</span>
                    <span class="fw-bold fs-4 text-primary">{{ number_format($b->total_price) }}đ</span>
                </div>
            </div>
            <div class="modal-footer border-top-0 d-flex justify-content-center pb-4">
                <button type="button" class="btn btn-primary rounded-pill px-5" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Cancel Modals --}}
@foreach($bookings as $b)
    @if($b->status === 'pending')
    <div class="modal fade" id="cancelBookingModal-{{ $b->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin.bookings.cancel', $b) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title fw-bold text-danger">Hủy Booking FB-{{ str_pad($b->id, 4, '0', STR_PAD_LEFT) }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">Bạn đang thao tác hủy đơn đặt sân của khách hàng <strong class="text-dark">{{ $b->user->name }}</strong> (Ngày: {{ $b->booking_date?->format('d/m/Y') }}, Khung giờ: {{ $b->start_time }} – {{ $b->end_time }}).</p>
                        <div class="mb-3 mt-4">
                            <label class="form-label small fw-medium">Lý do hủy (sẽ được lưu vào ghi chú)</label>
                            <textarea name="cancel_reason" class="form-control" rows="3" placeholder="Sân bảo trì đột xuất, khách bận không đến..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-danger">Xác nhận Hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

@endsection
