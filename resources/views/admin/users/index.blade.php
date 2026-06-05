@extends('layouts.admin')
@section('title', 'Người dùng — FieldBook Admin')
@section('content')
<h1 class="h4 fw-bold">Quản lý người dùng</h1>
<p class="text-muted small">Tổng {{ $users->count() }} tài khoản trong hệ thống.</p>

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

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
                            {{ $u->role === 'admin' ? 'Admin' : 'User' }}
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
                        @if($u->role !== 'admin')
                        <form action="{{ route('admin.users.toggleLock', $u) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('{{ $u->status === 'active' ? 'Khóa' : 'Mở khóa' }} tài khoản {{ $u->name }}?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $u->status === 'active' ? 'btn-outline-danger' : 'btn-outline-success' }} d-flex align-items-center gap-1">
                                <i class="bi {{ $u->status === 'active' ? 'bi-lock' : 'bi-unlock' }}"></i>
                                {{ $u->status === 'active' ? 'Khóa' : 'Mở khóa' }}
                            </button>
                        </form>
                        @endif
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
@endsection
