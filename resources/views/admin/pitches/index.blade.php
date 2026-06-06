@extends('layouts.admin')
@section('title', 'Quản lý sân — SanGo Admin')
@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Quản lý sân</h1>
    </div>
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-4 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#createPitchModal" style="background:var(--fb-primary);border-color:var(--fb-primary);">
        <i class="bi bi-plus-lg"></i> Thêm sân mới
    </button>
</div>

{{-- Filters and Search Bar --}}
<div class="card-fb mb-4 position-relative" style="overflow: visible !important; z-index: 100;">
    <div class="card-body p-3">
        <form action="{{ route('admin.pitches.index') }}" method="GET" class="row g-2 align-items-center" id="searchForm">
            <!-- Search -->
            <div class="col-12 col-md-5 position-relative">
                <div class="input-group">
                    <button type="submit" class="input-group-text bg-light border-0 text-muted ps-3 rounded-start-pill"><i class="bi bi-search"></i></button>
                    <input type="text" name="search" id="pitchSearchInput" class="form-control bg-light border-0 shadow-none rounded-end-pill" placeholder="Tìm kiếm tên sân, địa chỉ..." value="{{ request('search') }}" autocomplete="off">
                </div>
                <!-- Autocomplete Dropdown -->
                <ul class="dropdown-menu w-100 shadow-sm border-0 mt-1" id="pitchSearchDropdown" style="max-height: 250px; overflow-y: auto; display: none; position: absolute; z-index: 1050;">
                </ul>
            </div>
            <!-- Filter Type -->
            <div class="col-6 col-md-3">
                <select name="type" class="form-select bg-light border-0 shadow-none rounded-pill text-muted px-3" onchange="this.form.submit()">
                    <option value="">Tất cả loại sân</option>
                    <option value="football" {{ request('type') === 'football' ? 'selected' : '' }}>Bóng đá</option>
                    <option value="pickleball" {{ request('type') === 'pickleball' ? 'selected' : '' }}>Pickleball</option>
                </select>
            </div>
            <!-- Filter Status -->
            <div class="col-6 col-md-3">
                <select name="status" class="form-select bg-light border-0 shadow-none rounded-pill text-muted px-3" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <!-- Actions -->
            <div class="col-12 col-md-1 text-md-end text-center mt-2 mt-md-0">
                @if(request()->hasAny(['search', 'type', 'status']) && (request('search') || request('type') || request('status')))
                <a href="{{ route('admin.pitches.index') }}" class="btn btn-sm btn-light text-danger rounded-pill" title="Xóa bộ lọc">
                    <i class="bi bi-x-circle"></i> Bỏ lọc
                </a>
                @endif
            </div>
        </form>
        @if(request()->hasAny(['search', 'type', 'status']) && (request('search') || request('type') || request('status')))
        <div class="mt-3 text-muted small ps-2">
            Tìm thấy tổng cộng <span class="fw-bold text-dark">{{ $pitches->total() }}</span> sân phù hợp với bộ lọc.
        </div>
        @endif
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<!-- Global SweetAlert handles this -->
@endif

{{-- Pitches Table --}}
<div class="card-fb mt-4" id="pitches-table-container">
    <div class="table-responsive">
        <table class="table table-hover mb-0 small">
            <thead class="text-uppercase text-muted" style="font-size:0.7rem; background:rgba(0,0,0,0.02);">
                <tr>
                    <th class="px-4 py-3">Sân</th>
                    <th class="px-4 py-3">Loại</th>
                    <th class="px-4 py-3">Giá/giờ</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3 text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pitches as $p)
                <tr>
                    <td class="px-4 py-3">
                        <div class="d-flex align-items-center gap-3">
                            @if($p->image_url)
                            <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="rounded" style="width:56px;height:40px;object-fit:cover;">
                            @else
                            <div class="rounded d-flex align-items-center justify-content-center text-muted" style="width:56px;height:40px;background:rgba(31,78,121,0.08);">
                                <i class="bi bi-image"></i>
                            </div>
                            @endif
                            <div>
                                <span class="fw-medium d-block">{{ $p->name }}</span>
                                @if($p->address)
                                <span class="text-muted" style="font-size:0.75rem;"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ \Illuminate\Support\Str::limit($p->address, 50) }}</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge-status {{ $p->pitch_type === 'football' ? 'status-monthly' : 'status-hourly' }}">
                            {{ $p->pitch_type === 'football' ? 'Bóng đá' : 'Pickleball' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 fw-semibold">{{ number_format($p->price_per_hour) }}đ</td>
                    <td class="px-4 py-3">
                        @if($p->status === 'active')
                            <span class="badge-status status-confirmed">Active</span>
                        @elseif($p->status === 'maintenance')
                            <span class="badge-status status-pending">Bảo trì</span>
                        @else
                            <span class="badge-status status-cancelled">Inactive</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="d-flex justify-content-end gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary" title="Sửa"
                                    data-bs-toggle="modal" data-bs-target="#editPitchModal-{{ $p->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.pitches.destroy', $p) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Bạn chắc chắn muốn xóa sân {{ $p->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                @empty
                <tr><td colspan="5" class="text-muted text-center py-4">Chưa có sân nào trong hệ thống</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pitches->hasPages())
    <div class="card-footer bg-white border-top-0 py-3 d-flex justify-content-center">
        {{ $pitches->links('pagination::bootstrap-5') }}
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
/* Hide the "Showing 1 to 7 of 9 results" text from Laravel default bootstrap pagination */
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

{{-- Edit Modals --}}
@foreach($pitches as $p)
<div class="modal fade" id="editPitchModal-{{ $p->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <form action="{{ route('admin.pitches.update', $p) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Sửa sân — {{ $p->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Tên sân</label>
                        <input type="text" name="name" class="form-control" value="{{ $p->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Loại sân</label>
                        <select name="pitch_type" class="form-select">
                            <option value="football" {{ $p->pitch_type === 'football' ? 'selected' : '' }}>Bóng đá</option>
                            <option value="pickleball" {{ $p->pitch_type === 'pickleball' ? 'selected' : '' }}>Pickleball</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Mô tả</label>
                        <input type="text" name="description" class="form-control" value="{{ $p->description }}" placeholder="Mô tả ngắn...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" value="{{ $p->address }}" placeholder="Địa chỉ sân..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Giá cơ bản/giờ</label>
                        <input type="number" name="price_per_hour" class="form-control" value="{{ $p->price_per_hour }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Tải ảnh lên</label>
                        <input type="file" name="image_file" class="form-control" accept="image/*">
                        @if($p->image_url)
                        <div class="mt-2 small text-muted">
                            Ảnh hiện tại: <img src="{{ $p->image_url }}" height="30" class="rounded ms-2" style="object-fit:cover;">
                        </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ $p->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $p->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ $p->status === 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" style="background:var(--fb-primary);border-color:var(--fb-primary);">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Create Modal --}}
<div class="modal fade" id="createPitchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <form action="{{ route('admin.pitches.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Thêm sân mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Tên sân</label>
                        <input type="text" name="name" class="form-control" placeholder="Sân Bóng Đá D1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Loại sân</label>
                        <select name="pitch_type" class="form-select">
                            <option value="football">Bóng đá</option>
                            <option value="pickleball">Pickleball</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Mô tả</label>
                        <input type="text" name="description" class="form-control" placeholder="Mô tả ngắn...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" placeholder="Địa chỉ sân..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Giá cơ bản/giờ</label>
                        <input type="number" name="price_per_hour" class="form-control" placeholder="300000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Tải ảnh lên</label>
                        <input type="file" name="image_file" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="maintenance">Bảo trì</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" style="background:var(--fb-primary);border-color:var(--fb-primary);">Tạo sân</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('turbo:load', function() {
    const searchInput = document.getElementById('pitchSearchInput');
    const dropdown = document.getElementById('pitchSearchDropdown');
    
    // Only run on the Pitches index page
    if (!searchInput || !dropdown) return;
    
    // Prevent multiple bindings
    if (searchInput.dataset.autocompleteBound) return;
    searchInput.dataset.autocompleteBound = '1';

    const allPitches = @json($allPitchesForSearch ?? []);

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

        const matches = allPitches.filter(p => {
            return removeAccents(p.name).includes(val) || removeAccents(p.address).includes(val);
        }).slice(0, 8);

        if (matches.length > 0) {
            matches.forEach(match => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.className = 'dropdown-item small py-2 text-wrap cursor-pointer';
                a.href = 'javascript:void(0)';
                
                let text = match.name;
                if (match.address) text += ' - ' + match.address;
                a.textContent = text;
                
                a.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    searchInput.value = match.name;
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
