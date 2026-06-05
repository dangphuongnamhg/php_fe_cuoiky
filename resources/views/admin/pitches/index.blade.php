@extends('layouts.admin')
@section('title', 'Quản lý sân — FieldBook Admin')
@section('content')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h4 fw-bold">Quản lý sân bóng</h1>
        <p class="text-muted small mb-0">Tổng {{ $pitches->count() }} sân trong hệ thống.</p>
    </div>
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createPitchModal" style="background:var(--fb-primary);border-color:var(--fb-primary);">
        <i class="bi bi-plus-lg"></i> Thêm sân mới
    </button>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Pitches Table --}}
<div class="card-fb mt-4">
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
                            @if($p->image)
                            <img src="{{ $p->image }}" alt="{{ $p->name }}" class="rounded" style="width:56px;height:40px;object-fit:cover;">
                            @else
                            <div class="rounded d-flex align-items-center justify-content-center text-muted" style="width:56px;height:40px;background:rgba(31,78,121,0.08);">
                                <i class="bi bi-image"></i>
                            </div>
                            @endif
                            <span class="fw-medium">{{ $p->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge-status {{ $p->type === 'football' ? 'status-monthly' : 'status-hourly' }}">
                            {{ $p->type === 'football' ? 'Bóng đá' : 'Pickleball' }}
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

                {{-- Edit Modal for each pitch --}}
                <div class="modal fade" id="editPitchModal-{{ $p->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <form action="{{ route('admin.pitches.update', $p) }}" method="POST">
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
                                        <select name="type" class="form-select">
                                            <option value="football" {{ $p->type === 'football' ? 'selected' : '' }}>Bóng đá</option>
                                            <option value="pickleball" {{ $p->type === 'pickleball' ? 'selected' : '' }}>Pickleball</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-medium">Mô tả</label>
                                        <input type="text" name="description" class="form-control" value="{{ $p->description }}" placeholder="Mô tả ngắn...">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-medium">Giá cơ bản/giờ</label>
                                        <input type="number" name="price_per_hour" class="form-control" value="{{ $p->price_per_hour }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-medium">URL ảnh</label>
                                        <input type="url" name="image" class="form-control" value="{{ $p->image }}" placeholder="https://...">
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
                @empty
                <tr><td colspan="5" class="text-muted text-center py-4">Chưa có sân nào trong hệ thống</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createPitchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.pitches.store') }}" method="POST">
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
                        <select name="type" class="form-select">
                            <option value="football">Bóng đá</option>
                            <option value="pickleball">Pickleball</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Mô tả</label>
                        <input type="text" name="description" class="form-control" placeholder="Mô tả ngắn...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Giá cơ bản/giờ</label>
                        <input type="number" name="price_per_hour" class="form-control" placeholder="300000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">URL ảnh</label>
                        <input type="url" name="image" class="form-control" placeholder="https://...">
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
@endsection
