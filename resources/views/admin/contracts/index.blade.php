@extends('layouts.admin')
@section('title', 'Hợp đồng tháng — SanGo Admin')
@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Hợp đồng tháng</h1>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<!-- Alert handled globally via SweetAlert in layout -->
@endif

{{-- Filters --}}
<div class="card-fb p-4 mt-3 position-relative" style="overflow: visible !important; z-index: 100;">
    <form method="GET" action="{{ route('admin.contracts.index') }}" class="d-flex flex-wrap gap-3 align-items-end" id="searchForm">
        <!-- Search -->
        <div class="flex-grow-1 position-relative" style="max-width: 300px;">
            <label class="form-label small text-muted mb-1">Tìm kiếm</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="search" id="contractSearchInput" class="form-control border-start-0 ps-0" placeholder="Mã HĐ, Tên khách..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <!-- Autocomplete Dropdown -->
            <ul class="dropdown-menu w-100 shadow-sm border-0 mt-1" id="contractSearchDropdown" style="max-height: 250px; overflow-y: auto; display: none; position: absolute; z-index: 1050;">
            </ul>
        </div>
        <div>
            <label class="form-label small text-muted mb-1">Trạng thái HĐ</label>
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                <option value="renewed" {{ request('status') === 'renewed' ? 'selected' : '' }}>Đã gia hạn</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
            </select>
        </div>
        <div>
            <label class="form-label small text-muted mb-1">Trạng thái Khóa (Lock)</label>
            <select name="lock_status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả</option>
                <option value="active" {{ request('lock_status') === 'active' ? 'selected' : '' }}>Đang khóa</option>
                <option value="expired" {{ request('lock_status') === 'expired' ? 'selected' : '' }}>Hết hạn khóa</option>
                <option value="released" {{ request('lock_status') === 'released' ? 'selected' : '' }}>Đã giải phóng</option>
            </select>
        </div>
        <div class="d-flex align-items-end gap-2">
            <button type="submit" class="d-none">Lọc</button>
            @if(request()->hasAny(['search', 'status', 'lock_status']) && (request('search') || request('status') || request('lock_status')))
                <a href="{{ route('admin.contracts.index') }}" class="btn btn-sm btn-light text-danger">Bỏ lọc</a>
            @endif
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
                    <td class="px-4 py-3">{{ $c->month_start ? \Carbon\Carbon::parse($c->month_start)->format('m/Y') : '—' }}</td>
                    <td class="px-4 py-3">{{ $c->day_of_week }} {{ $c->start_time }}–{{ $c->end_time }}</td>
                    <td class="px-4 py-3">
                        @if($c->status === 'active')
                            <span class="badge-status status-confirmed">Đang hoạt động</span>
                        @elseif($c->status === 'renewed')
                            <span class="badge-status status-monthly">Đã gia hạn</span>
                        @elseif($c->status === 'cancelled')
                            <span class="badge-status status-cancelled">Đã hủy</span>
                        @else
                            <span class="badge-status status-cancelled">{{ ucfirst($c->status) }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($c->locks && $c->locks->where('status', 'active')->count() > 0)
                            <span class="badge-status status-pending">🔒 Đang giữ chỗ</span>
                        @else
                            <span class="badge-status status-cancelled">Không khóa</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <button type="button" class="btn btn-sm btn-light text-primary border-0 shadow-sm" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;" title="Xem chi tiết" data-bs-toggle="modal" data-bs-target="#detailContractModal-{{ $c->id }}">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                            <!-- Button moved to detail modal -->
                        </div>
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

{{-- Detail Modals --}}
@foreach($contracts as $c)
<div class="modal fade" id="detailContractModal-{{ $c->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-bottom-0" style="background: linear-gradient(135deg, rgba(13,110,253,0.05), rgba(13,110,253,0.1));">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Chi tiết Hợp đồng</h5>
                        <div class="text-primary small fw-semibold">CT-{{ str_pad($c->id, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="bg-light rounded-4 p-3 mb-4 border" style="border-color: rgba(0,0,0,0.05) !important;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-circle flex-shrink-0" style="width:48px;height:48px;background:var(--fb-secondary);font-size:1.2rem;">
                            {{ mb_substr($c->user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-muted small fw-medium mb-1">Khách hàng</div>
                            <div class="fw-bold text-dark fs-5 lh-1">{{ $c->user->name }}</div>
                            <div class="text-muted small mt-1"><i class="bi bi-envelope me-1"></i>{{ $c->user->email }}</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted small"><i class="bi bi-geo-alt me-2"></i>Sân</span>
                        <span class="fw-bold text-dark text-end">{{ $c->pitch->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted small"><i class="bi bi-calendar-month me-2"></i>Tháng áp dụng</span>
                        <span class="fw-bold text-dark text-end">{{ $c->month_start ? \Carbon\Carbon::parse($c->month_start)->format('m/Y') : '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted small"><i class="bi bi-clock me-2"></i>Lịch đá cố định</span>
                        <span class="fw-bold text-dark text-end">{{ $c->day_of_week }} | <span class="text-primary">{{ $c->start_time }} – {{ $c->end_time }}</span></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="text-muted small"><i class="bi bi-check-circle me-2"></i>Trạng thái</span>
                        <span class="fw-bold text-dark text-end">
                            @if($c->status === 'active') <span class="text-success">Đang hoạt động</span>
                            @elseif($c->status === 'renewed') <span class="text-primary">Đã gia hạn</span>
                            @else <span class="text-danger">Đã hủy</span> @endif
                        </span>
                    </div>
                    @if($c->locks && $c->locks->count() > 0)
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-calendar-range text-primary me-2"></i>Chi tiết các ngày giữ chỗ</h6>
                        <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                            @foreach($c->locks->sortBy('lock_date') as $lock)
                                <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <div>
                                        <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($lock->lock_date)->format('d/m/Y') }}</span>
                                        <br>
                                        @if($lock->status === 'active')
                                            <span class="badge bg-success bg-opacity-10 text-success small border border-success border-opacity-25 mt-1">Đang giữ chỗ</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary small border border-secondary border-opacity-25 mt-1">Đã giải phóng</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($lock->status === 'active')
                                            <form action="{{ route('admin.contracts.release', $lock->id) }}" method="POST" data-turbo="false" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Mở khóa ngày này" onclick="return confirm('Bạn có chắc muốn mở khóa cho ngày {{ \Carbon\Carbon::parse($lock->lock_date)->format('d/m/Y') }} không?');">
                                                    <i class="bi bi-unlock"></i> Nghỉ
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.contracts.relock', $lock->id) }}" method="POST" data-turbo="false" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Khóa lại ngày này" onclick="return confirm('Bạn muốn khóa lại ngày {{ \Carbon\Carbon::parse($lock->lock_date)->format('d/m/Y') }}?');">
                                                    <i class="bi bi-lock"></i> Khóa lại
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-top-0 d-flex justify-content-center pb-4">
                <button type="button" class="btn btn-primary rounded-pill px-5" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
document.addEventListener('turbo:load', function() {
    const searchInput = document.getElementById('contractSearchInput');
    const dropdown = document.getElementById('contractSearchDropdown');
    
    if (!searchInput || !dropdown) return;
    
    if (searchInput.dataset.autocompleteBound) return;
    searchInput.dataset.autocompleteBound = '1';

    const allContracts = @json($contracts->items() ?? []);

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

        const matches = allContracts.filter(c => {
            const ctId = 'ct-' + String(c.id).padStart(4, '0');
            return removeAccents(c.user.name).includes(val) || ctId.includes(val);
        }).slice(0, 8);

        if (matches.length > 0) {
            matches.forEach(match => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.className = 'dropdown-item small py-2 text-wrap cursor-pointer d-flex align-items-center gap-2';
                a.href = 'javascript:void(0)';
                
                const ctId = 'CT-' + String(match.id).padStart(4, '0');
                a.innerHTML = `<span class="badge bg-light text-dark border border-secondary">${ctId}</span> <span>${match.user.name}</span>`;
                
                a.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    searchInput.value = match.user.name;
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
        renderDropdown(removeAccents(this.value.trim()));
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
