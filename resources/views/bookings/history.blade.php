@extends('layouts.app')
@section('title', 'Lịch sử đặt sân — SanGo')
@section('content')

<div class="container py-4" style="max-width:1100px;">
    <h1 class="fs-4 fw-bold">Lịch sử đặt sân</h1>
    <p class="text-muted small">Quản lý tất cả các đơn đặt sân của bạn.</p>

    <div class="card-fb mt-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:.875rem;">
                <thead style="background:#f0f4f8;">
                    <tr>
                        <th class="px-4 py-3 text-muted small text-uppercase">Mã đơn</th>
                        <th class="px-4 py-3 text-muted small text-uppercase">Sân</th>
                        <th class="px-4 py-3 text-muted small text-uppercase">Ngày</th>
                        <th class="px-4 py-3 text-muted small text-uppercase">Khung giờ</th>
                        <th class="px-4 py-3 text-muted small text-uppercase">Loại</th>
                        <th class="px-4 py-3 text-muted small text-uppercase">Trạng thái</th>
                        <th class="px-4 py-3 text-muted small text-uppercase text-end">Tổng tiền</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings ?? [] as $b)
                    <tr>
                        <td class="px-4 py-3 font-monospace small">{{ $b->code }}</td>
                        <td class="px-4 py-3 fw-medium">{{ $b->pitch_name }}</td>
                        <td class="px-4 py-3">{{ $b->date }}</td>
                        <td class="px-4 py-3">{{ $b->time_slot }}</td>
                        <td class="px-4 py-3">
                            <span class="badge-status {{ $b->type === 'monthly' ? 'status-monthly' : 'status-hourly' }}">
                                {{ $b->type === 'monthly' ? 'Tháng cố định' : 'Theo giờ' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @switch($b->status)
                                @case('confirmed')
                                    <span class="badge-status status-confirmed">Đã xác nhận</span>
                                    @break
                                @case('pending')
                                    <span class="badge-status status-pending">Chờ xác nhận</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge-status status-cancelled">Đã hủy</span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-4 py-3 text-end fw-semibold">{{ number_format($b->total) }}đ</td>
                        <td class="px-4 py-3 text-end">
                            @if(in_array($b->status, ['pending', 'confirmed']))
                            <form method="POST" action="{{ route('bookings.cancel', isset($b->id) ? $b->id : 0) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-2 px-3" style="font-size:.75rem;" onclick="return confirm('Bạn chắc chắn muốn hủy?')">
                                    Hủy
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x fs-2 mb-2 d-block"></i>
                            Bạn chưa có lịch sử đặt sân nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const payment = urlParams.get('payment');
    const msg = urlParams.get('msg');
    
    if (payment === 'success' || payment === 'monthly_success') {
        Swal.fire({
            icon: 'success',
            title: 'Thanh toán thành công',
            text: 'Đơn đặt sân của bạn đã được thanh toán và xác nhận!',
            confirmButtonText: 'Tuyệt vời',
            confirmButtonColor: 'var(--fb-primary)'
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    } else if (payment === 'failed' || payment === 'error' || payment === 'conflict') {
        Swal.fire({
            icon: 'error',
            title: 'Thanh toán thất bại',
            text: msg || 'Có lỗi xảy ra trong quá trình thanh toán.',
            confirmButtonText: 'Đóng'
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }
});
</script>
@endpush
