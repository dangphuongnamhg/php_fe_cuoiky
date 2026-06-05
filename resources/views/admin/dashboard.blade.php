@extends('layouts.admin')
@section('title', 'Dashboard — FieldBook Admin')
@section('content')
<h1 class="h4 fw-bold">Dashboard tổng quan</h1>
<p class="text-muted small">Tổng hợp hoạt động hệ thống FieldBook hôm nay.</p>

{{-- KPI Cards --}}
<div class="row g-3 mt-2">
    <div class="col-sm-6 col-lg-4">
        <div class="card-fb p-4 h-100 d-flex flex-column justify-content-center">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(31,78,121,0.1);color:var(--fb-primary);">
                    <i class="bi bi-graph-up-arrow fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium text-uppercase" style="letter-spacing:0.05em;font-size:0.7rem;">Doanh thu tháng</div>
                    <div class="fs-4 fw-bold" style="color:var(--fb-primary);">{{ number_format($totalRevenue) }}đ</div>
                    <div class="small fw-semibold text-success"><i class="bi bi-arrow-up-right me-1"></i>+12.4%</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card-fb p-4 h-100 d-flex flex-column justify-content-center">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(46,117,182,0.15);color:var(--fb-secondary);">
                    <i class="bi bi-calendar-check fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium text-uppercase" style="letter-spacing:0.05em;font-size:0.7rem;">Booking hôm nay</div>
                    <div class="fs-4 fw-bold" style="color:var(--fb-primary);">{{ $todayBookings }}</div>
                    <div class="small fw-semibold text-success"><i class="bi bi-arrow-up-right me-1"></i>+5 vs hôm qua</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card-fb p-4 h-100 d-flex flex-column justify-content-center">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(243,156,18,0.15);color:#d97706;">
                    <i class="bi bi-clock fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium text-uppercase" style="letter-spacing:0.05em;font-size:0.7rem;">Pending cần duyệt</div>
                    <div class="fs-4 fw-bold" style="color:#d97706;">{{ $pendingCount }}</div>
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
    <div class="bar-chart mt-4 d-flex align-items-end" style="height:200px;">
        @foreach($revenue as $i => $v)
        <div class="d-flex flex-column align-items-center gap-2 flex-fill h-100 justify-content-end group-hover" style="cursor:pointer;">
            <div class="small fw-semibold text-primary opacity-0 hover-opacity-100 transition-all">{{ $v }}M</div>
            <div class="w-100 rounded-top" style="height:{{ ($v/$max)*80 }}%; background:linear-gradient(to top, var(--fb-primary), var(--fb-secondary)); max-width:48px; transition:all 0.3s;"></div>
            <div class="text-muted fw-medium" style="font-size:0.75rem;">{{ $days[$i] }}</div>
        </div>
        @endforeach
        <style>
            .group-hover:hover .hover-opacity-100 { opacity: 1 !important; transform: translateY(-4px); }
            .group-hover:hover .rounded-top { opacity: 0.8; }
            .opacity-0 { opacity: 0; }
        </style>
    </div>
</div>

{{-- Tables --}}
<div class="row g-4 mt-2">
    {{-- Expiring Contracts --}}
    <div class="col-lg-6">
        <div class="card-fb p-4">
            <h6 class="fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-circle" style="color:var(--fb-primary);"></i>
                Hợp đồng tháng sắp hết hạn
            </h6>
            <div class="table-responsive mt-3">
                <table class="table table-sm small mb-0">
                    <thead class="text-uppercase text-muted" style="font-size:0.7rem;">
                        <tr>
                            <th>Mã HĐ</th>
                            <th>Khách hàng</th>
                            <th>Sân</th>
                            <th>Hết hạn</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expiringContracts as $c)
                        <tr>
                            <td class="font-monospace text-primary fw-medium">CT-{{ $c->id }}</td>
                            <td class="fw-medium">{{ $c->user->name }}</td>
                            <td>{{ $c->pitch->name }}</td>
                            <td>{{ $c->last_occurrence_date?->format('d/m') }}</td>
                            <td><span class="badge-status status-pending">Sắp hết hạn</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-muted text-center py-3">Không có hợp đồng sắp hết hạn</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Active Locks --}}
    <div class="col-lg-6">
        <div class="card-fb p-4">
            <h6 class="fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-lock" style="color:var(--fb-primary);"></i>
                Lock đang active
            </h6>
            <div class="table-responsive mt-3">
                <table class="table table-sm small mb-0">
                    <thead class="text-uppercase text-muted" style="font-size:0.7rem;">
                        <tr>
                            <th>Sân</th>
                            <th>Khung giờ</th>
                            <th>Khách hàng</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeLocks as $lock)
                        <tr>
                            <td>{{ $lock->pitch->name }}</td>
                            <td>{{ $lock->start_time }}–{{ $lock->end_time }}</td>
                            <td>{{ $lock->user->name }}</td>
                            <td class="text-end">
                                <form action="{{ route('admin.contracts.release', $lock) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Bạn chắc chắn muốn giải phóng lock này?')">
                                        <i class="bi bi-unlock me-1"></i>Giải phóng
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-muted text-center py-3">Không có lock đang active</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
