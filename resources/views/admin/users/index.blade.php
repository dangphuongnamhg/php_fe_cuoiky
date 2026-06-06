@extends('layouts.admin')
@section('title', 'Người dùng — SanGo Admin')
@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Quản lý người dùng</h1>
    </div>
    <div>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-plus-lg"></i> Thêm người dùng
        </button>
    </div>
</div>

{{-- Filters --}}
<div class="card-fb mb-4" style="overflow: visible !important;">
    <form method="GET" action="{{ route('admin.users.index') }}" class="card-body p-3 d-flex flex-wrap gap-3 align-items-end" id="searchForm" data-turbo="false">
        <div class="flex-grow-1 position-relative" style="max-width: 300px;">
            <label class="form-label small text-muted mb-1">Tìm kiếm</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="search" id="userSearchInput" class="form-control border-start-0 ps-0" placeholder="Tên, Email..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <ul class="dropdown-menu w-100 shadow-sm" id="userSearchDropdown" style="display:none; position:absolute; top:100%; left:0; z-index:1050; max-height:250px; overflow-y:auto; border-radius:0.5rem; margin-top:4px;">
            </ul>
        </div>
        <div>
            <label class="form-label small text-muted mb-1">Trạng thái</label>
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tất cả</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="locked" {{ request('status') === 'locked' ? 'selected' : '' }}>Đã khóa</option>
            </select>
        </div>
        <div class="d-flex align-items-end gap-2">
            <button type="submit" class="d-none">Lọc</button>
            @if(request()->hasAny(['search', 'status']) && (request('search') || request('status')))
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light text-danger">Bỏ lọc</a>
            @endif
        </div>
    </form>
</div>

<div class="card-fb mt-4">
    <div class="table-responsive">
        <table class="table table-hover mb-0 small">
            <thead class="text-uppercase text-muted" style="font-size:0.7rem; background:rgba(0,0,0,0.02);">
                <tr>
                    <th class="px-4 py-3">Tên</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3">Ngày tạo</th>
                    <th class="px-4 py-3 text-end">Booking</th>
                    <th class="px-4 py-3">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td class="px-4 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-circle shadow-sm" style="width:36px;height:36px;">{{ mb_substr($u->name, -2, 1) }}</div>
                            <span class="fw-medium">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-muted">{{ $u->email }}</td>
                    <td class="px-4 py-3">
                        <span class="badge-status {{ $u->role === 'admin' ? 'status-hourly' : 'status-monthly' }}">
                            {{ ucfirst($u->role) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge-status {{ $u->status === 'active' ? 'status-confirmed' : 'status-cancelled' }}">
                            {{ $u->status === 'active' ? 'Active' : 'Locked' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $u->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-end fw-semibold">{{ $u->bookings_count }}</td>
                    <td class="px-4 py-3">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-light text-primary border-0 shadow-sm" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $u->id }}" title="Chỉnh sửa">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            @if($u->role !== 'admin')
                            <form action="{{ route('admin.users.toggleLock', $u) }}" method="POST" class="m-0" data-turbo="false" onsubmit="return confirm('{{ $u->status === 'active' ? 'Khóa' : 'Mở khóa' }} tài khoản {{ $u->name }}?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-light {{ $u->status === 'active' ? 'text-warning' : 'text-success' }} border-0 shadow-sm" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;" title="{{ $u->status === 'active' ? 'Khóa' : 'Mở khóa' }}">
                                    <i class="bi {{ $u->status === 'active' ? 'bi-lock' : 'bi-unlock' }}"></i>
                                </button>
                            </form>

                            <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="m-0" data-turbo="false" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng {{ $u->name }} không? Việc này không thể hoàn tác.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger border-0 shadow-sm" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if(method_exists($users, 'hasPages') && $users->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $users->withQueryString()->links() }}
</div>
@endif

{{-- Modals --}}
@foreach($users as $u)
{{-- Edit Modal --}}
<div class="modal fade" id="editUserModal-{{ $u->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Chỉnh sửa người dùng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.update', $u) }}" method="POST" data-turbo="false">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Tên người dùng <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Mật khẩu mới</label>
                        <input type="password" name="password" class="form-control" placeholder="Để trống nếu không muốn đổi">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Phân quyền</label>
                            <select name="role" class="form-select">
                                <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $u->status === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="locked" {{ $u->status === 'locked' ? 'selected' : '' }}>Khóa</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 py-3">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary px-4">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Create Modal --}}
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Thêm người dùng mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST" data-turbo="false">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Tên người dùng <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Phân quyền</label>
                            <select name="role" class="form-select">
                                <option value="user" selected>User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="active" selected>Hoạt động</option>
                                <option value="locked">Khóa</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 py-3">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary px-4">Tạo người dùng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('turbo:load', function() {
    const searchInput = document.getElementById('userSearchInput');
    const dropdown = document.getElementById('userSearchDropdown');
    
    if (!searchInput || !dropdown) return;
    
    const allUsers = @json($allUsersForSearch ?? []);

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

        const matches = allUsers.filter(u => {
            return removeAccents(u.name).includes(val) || removeAccents(u.email).includes(val);
        }).slice(0, 8);

        if (matches.length > 0) {
            matches.forEach(match => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.className = 'dropdown-item small py-2 text-wrap cursor-pointer d-flex flex-column';
                a.href = 'javascript:void(0)';
                
                a.innerHTML = `<span class="fw-bold">${match.name}</span><span class="text-muted" style="font-size: 0.75rem;">${match.email}</span>`;
                
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
