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
                            <form method="POST" action="{{ route('bookings.cancel', $b) }}" class="d-inline">
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
                    {{-- Mock data fallback --}}
                    @foreach([
                        (object)['code'=>'FB-202607-0042','pitch_name'=>'Sân Bóng Đá A1','date'=>'06/06/2026','time_slot'=>'18:00-20:00','type'=>'hourly','status'=>'confirmed','total'=>720000],
                        (object)['code'=>'FB-202607-0035','pitch_name'=>'Sân Pickleball C1','date'=>'Tháng 06/2026','time_slot'=>'T4 19:00-20:30','type'=>'monthly','status'=>'confirmed','total'=>3600000],
                        (object)['code'=>'FB-202606-0028','pitch_name'=>'Sân Bóng Đá B2','date'=>'29/05/2026','time_slot'=>'07:00-08:30','type'=>'hourly','status'=>'pending','total'=>675000],
                        (object)['code'=>'FB-202605-0019','pitch_name'=>'Sân Pickleball C2','date'=>'12/05/2026','time_slot'=>'20:00-21:00','type'=>'hourly','status'=>'cancelled','total'=>220000],
                    ] as $b)
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
                            @if($b->status === 'confirmed')
                                <span class="badge-status status-confirmed">Đã xác nhận</span>
                            @elseif($b->status === 'pending')
                                <span class="badge-status status-pending">Chờ xác nhận</span>
                            @else
                                <span class="badge-status status-cancelled">Đã hủy</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-end fw-semibold">{{ number_format($b->total) }}đ</td>
                        <td class="px-4 py-3 text-end">
                            @if(in_array($b->status, ['pending', 'confirmed']))
                            <button class="btn btn-outline-danger btn-sm rounded-2 px-3" style="font-size:.75rem;">Hủy</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
