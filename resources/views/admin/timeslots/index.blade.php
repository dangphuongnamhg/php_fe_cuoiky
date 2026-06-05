@extends('layouts.admin')
@section('title', 'Khung giờ — FieldBook Admin')
@section('content')
<h1 class="h4 fw-bold">Quản lý khung giờ</h1>
<p class="text-muted small">Cấu hình khung giờ hoạt động cho từng sân.</p>

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Filters --}}
<div class="card-fb p-4 mt-3">
    <form method="GET" action="{{ route('admin.timeslots.index') }}" class="d-flex flex-wrap gap-3 align-items-end">
        <div>
            <label class="form-label small text-muted mb-1">Sân</label>
            <select name="pitch_id" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach($pitches as $p)
                <option value="{{ $p->id }}" {{ request('pitch_id', $pitches->first()?->id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small text-muted mb-1">Lọc theo thứ</label>
            <select name="day" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả</option>
                @php $dayLabels = ['T2' => 'Thứ 2', 'T3' => 'Thứ 3', 'T4' => 'Thứ 4', 'T5' => 'Thứ 5', 'T6' => 'Thứ 6', 'T7' => 'Thứ 7', 'CN' => 'Chủ nhật']; @endphp
                @foreach($dayLabels as $key => $label)
                <option value="{{ $key }}" {{ request('day') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>

{{-- Stats --}}
@php
    $activeCount = $timeslots->where('is_active', true)->count();
    $inactiveCount = $timeslots->where('is_active', false)->count();
@endphp
<div class="row g-3 mt-2">
    <div class="col-sm-6 col-lg-3">
        <div class="card-fb p-4 h-100 d-flex flex-column justify-content-center">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(25,135,84,0.12);color:#198754;">
                    <i class="bi bi-check-circle fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium text-uppercase" style="letter-spacing:0.05em;font-size:0.7rem;">Slot active</div>
                    <div class="fs-4 fw-bold text-success">{{ $activeCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card-fb p-4 h-100 d-flex flex-column justify-content-center">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(108,117,125,0.12);color:#6c757d;">
                    <i class="bi bi-x-circle fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium text-uppercase" style="letter-spacing:0.05em;font-size:0.7rem;">Slot inactive</div>
                    <div class="fs-4 fw-bold text-secondary">{{ $inactiveCount }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Timeslots Table grouped by day --}}
@php
    $dayOrder = ['T2','T3','T4','T5','T6','T7','CN'];
    $grouped = $timeslots->groupBy('day_of_week');
@endphp

@foreach($dayOrder as $day)
    @if(!request('day') || request('day') === $day)
    @php $daySlots = $grouped->get($day, collect()); @endphp
    @if($daySlots->isNotEmpty())
    <div class="card-fb mt-4">
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-calendar3" style="color:var(--fb-primary);"></i>
                {{ $dayLabels[$day] ?? $day }}
            </h6>
            <span class="badge text-bg-light text-muted">{{ $daySlots->count() }} khung giờ</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 small">
                <thead class="text-uppercase text-muted" style="font-size:0.7rem; background:rgba(0,0,0,0.02);">
                    <tr>
                        <th class="px-4 py-3">Khung giờ</th>
                        <th class="px-4 py-3">Bắt đầu</th>
                        <th class="px-4 py-3">Kết thúc</th>
                        <th class="px-4 py-3">Trạng thái</th>
                        <th class="px-4 py-3 text-end">Bật/Tắt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($daySlots->sortBy('start_time') as $ts)
                    <tr>
                        <td class="px-4 py-3 fw-medium">{{ $ts->start_time }}–{{ $ts->end_time }}</td>
                        <td class="px-4 py-3">{{ $ts->start_time }}</td>
                        <td class="px-4 py-3">{{ $ts->end_time }}</td>
                        <td class="px-4 py-3">
                            @if($ts->is_active)
                                <span class="badge-status status-confirmed">Active</span>
                            @else
                                <span class="badge-status status-cancelled">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-end">
                            <form action="{{ route('admin.timeslots.toggle', $ts) }}" method="POST" class="d-inline">
                                @csrf
                                <div class="form-check form-switch d-flex justify-content-end mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           {{ $ts->is_active ? 'checked' : '' }}
                                           onchange="this.form.submit()"
                                           style="cursor:pointer;width:2.5em;height:1.3em;">
                                </div>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endif
@endforeach

@if($timeslots->isEmpty())
<div class="card-fb mt-4 p-5 text-center">
    <i class="bi bi-clock-history fs-1 text-muted"></i>
    <p class="text-muted mt-3">Chưa có khung giờ nào cho sân này.</p>
</div>
@endif
@endsection
