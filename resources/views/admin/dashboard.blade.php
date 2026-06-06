@extends('layouts.admin')
@section('title', 'Dashboard — SanGo Admin')
@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Dashboard tổng quan</h1>
    </div>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mt-2">
    <div class="col-sm-6 col-lg-4">
        <div class="card p-4 h-100 border-0 rounded-4 shadow-sm hover-elevate transition-all">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;background:rgba(31,78,121,0.1);color:var(--fb-primary);">
                    <i class="bi bi-wallet2 fs-5"></i>
                </div>
                <div>
                    <div class="text-muted fw-bold text-uppercase" style="letter-spacing:1px;font-size:0.7rem;">DOANH THU THÁNG</div>
                    <div class="fs-2 fw-bolder text-dark lh-1 mt-1 mb-1">{{ number_format($totalRevenue) }}đ</div>
                    <div class="small fw-semibold text-success"><i class="bi bi-arrow-up-right me-1"></i>+12.4%</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card p-4 h-100 border-0 rounded-4 shadow-sm hover-elevate transition-all">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;background:rgba(46,117,182,0.15);color:var(--fb-secondary);">
                    <i class="bi bi-calendar-check fs-5"></i>
                </div>
                <div>
                    <div class="text-muted fw-bold text-uppercase" style="letter-spacing:1px;font-size:0.7rem;">SỐ ĐƠN ĐẶT SÂN</div>
                    <div class="fs-2 fw-bolder text-dark lh-1 mt-1 mb-1">{{ number_format($totalBookings) }}</div>
                    <div class="small fw-semibold text-success"><i class="bi bi-calendar-day me-1"></i>+{{ $todayBookings }} đơn hôm nay</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card p-4 h-100 border-0 rounded-4 shadow-sm hover-elevate transition-all">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;background:rgba(245,158,11,0.15);color:#d97706;">
                    <i class="bi bi-clock-history fs-5"></i>
                </div>
                <div>
                    <div class="text-muted fw-bold text-uppercase" style="letter-spacing:1px;font-size:0.7rem;">ĐƠN CẦN DUYỆT</div>
                    <div class="fs-2 fw-bolder text-dark lh-1 mt-1 mb-1">{{ $pendingCount }}</div>
                    <div class="small fw-semibold text-warning"><i class="bi bi-exclamation-circle me-1"></i>Cần xử lý ngay</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Revenue Chart --}}
<div class="card-fb p-4 mt-4">
    <div class="d-flex justify-content-between">
        <h6 class="fw-semibold">Doanh thu 7 ngày qua</h6>
        <span class="text-muted" style="font-size:0.7rem;">Triệu VND</span>
    </div>
    @php
        $revenue = $revenueChart ?? [12, 18, 14, 22, 28, 35, 31];
        $days = ['T2','T3','T4','T5','T6','T7','CN'];
        $max = max($revenue) ?: 1;
    @endphp
    <div class="bar-chart mt-4 d-flex align-items-end position-relative" style="height:200px;">
        <div class="position-absolute w-100 h-100" style="background-image: repeating-linear-gradient(to bottom, transparent, transparent 39px, rgba(0,0,0,0.06) 39px, rgba(0,0,0,0.06) 40px); z-index:0;"></div>
        @foreach($revenue as $i => $v)
        <div class="d-flex flex-column align-items-center gap-2 flex-fill h-100 justify-content-end group-hover position-relative" style="cursor:pointer; z-index:1;">
            <div class="small fw-bold text-primary opacity-0 hover-opacity-100 transition-all">{{ $v }}M</div>
            <div class="w-100" style="height:{{ ($v/$max)*80 }}%; background:linear-gradient(to top, var(--fb-secondary), var(--fb-primary)); max-width:40px; border-radius: 8px 8px 0 0; transition:all 0.3s; box-shadow: 0 4px 10px rgba(31,78,121,0.15);"></div>
            <div class="text-muted fw-bold" style="font-size:0.75rem;">{{ $days[$i] }}</div>
        </div>
        @endforeach
        <style>
            .group-hover:hover .hover-opacity-100 { opacity: 1 !important; transform: translateY(-4px); }
            .group-hover:hover .w-100 { filter: brightness(1.1); transform: scaleY(1.02); transform-origin: bottom; }
            .opacity-0 { opacity: 0; }
            .hover-elevate:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important; }
            .table-modern td, .table-modern th { border-bottom: 1px solid rgba(0,0,0,0.03); vertical-align: middle; }
        </style>
    </div>
</div>

{{-- Tables --}}
<div class="row g-4 mt-2">
    {{-- Expiring Contracts --}}
    <div class="col-lg-6">
        <div class="card border-0 rounded-4 shadow-sm p-4 h-100">
            <h6 class="fw-bold d-flex align-items-center gap-2 mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger" style="width:32px;height:32px;">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
                Hợp đồng tháng sắp hết hạn
            </h6>
            <div class="table-responsive">
                <table class="table table-borderless table-modern table-hover small mb-0">
                    <thead class="text-uppercase text-muted" style="font-size:0.7rem; letter-spacing:0.5px;">
                        <tr>
                            <th class="ps-3 py-3">Mã HĐ</th>
                            <th class="py-3">Khách hàng</th>
                            <th class="py-3">Sân</th>
                            <th class="py-3">Hết hạn</th>
                            <th class="py-3 text-end pe-3">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expiringContracts as $c)
                        <tr>
                            <td class="ps-3 font-monospace fw-bold text-dark">CT-{{ $c->id }}</td>
                            <td class="fw-medium text-dark">{{ $c->user->name }}</td>
                            <td class="text-muted">{{ $c->pitch->name }}</td>
                            <td class="fw-medium">{{ $c->last_occurrence_date?->format('d/m') }}</td>
                            <td class="text-end pe-3"><span class="badge rounded-pill bg-warning text-dark px-3 py-2 border border-warning">Sắp hết hạn</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-muted text-center py-4">Không có hợp đồng sắp hết hạn</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Active Locks --}}
    <div class="col-lg-6">
        <div class="card border-0 rounded-4 shadow-sm p-4 h-100">
            <h6 class="fw-bold d-flex align-items-center gap-2 mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width:32px;height:32px;">
                    <i class="bi bi-lock"></i>
                </div>
                Lock đang active
            </h6>
            <div class="table-responsive">
                <table class="table table-borderless table-modern table-hover small mb-0">
                    <thead class="text-uppercase text-muted" style="font-size:0.7rem; letter-spacing:0.5px;">
                        <tr>
                            <th class="ps-3 py-3">Sân</th>
                            <th class="py-3">Khung giờ</th>
                            <th class="py-3">Khách hàng</th>
                            <th class="py-3 text-end pe-3">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeLocks as $lock)
                        <tr>
                            <td class="ps-3 fw-medium text-dark">{{ $lock->pitch->name }}</td>
                            <td class="text-muted">{{ $lock->start_time }}–{{ $lock->end_time }}</td>
                            <td class="fw-medium text-dark">{{ $lock->user->name }}</td>
                            <td class="text-end pe-3">
                                <form action="{{ route('admin.contracts.release', $lock) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm rounded-pill px-3 fw-bold" style="background:#fef3c7; color:#92400e; border:none; font-size:0.75rem;" onclick="return confirm('Bạn chắc chắn muốn giải phóng lock này?')">
                                        <i class="bi bi-unlock me-1"></i>Giải phóng
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-muted text-center py-4">Không có lock đang active</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($activeLocks->hasPages())
            <div class="mt-3 d-flex justify-content-end">
                {{ $activeLocks->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
