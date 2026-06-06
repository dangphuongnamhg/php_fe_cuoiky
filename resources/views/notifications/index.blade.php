@extends('layouts.app')
@section('title', 'Thông báo — SanGo')
@section('content')

<div class="container py-4" style="max-width:768px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fs-4 fw-bold">Thông báo</h1>
            <p class="text-muted small mb-0">Cập nhật về đặt sân, thanh toán và hợp đồng.</p>
        </div>
        <button class="btn btn-outline-primary btn-sm rounded-3 px-3" id="mark-all-read" style="font-size:.8rem;">
            <i class="bi bi-check2-all me-1"></i>Đánh dấu tất cả đã đọc
        </button>
    </div>

    <div class="d-flex flex-column gap-2" id="notification-list">
        @forelse($notifications ?? [] as $n)
        <div class="card-fb d-flex gap-3 p-4 {{ !$n->read_at ? 'notification-unread' : '' }}">
            @if(!$n->read_at)
            <div class="notification-dot"></div>
            @endif
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                    <h6 class="fw-semibold mb-1 small">{{ $n->title }}</h6>
                    <span class="text-muted" style="font-size:.7rem;flex-shrink:0;">{{ $n->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-muted small mb-0">{{ $n->body }}</p>
            </div>
        </div>
        @empty
        {{-- Mock notifications --}}
        @foreach([
            (object)['title'=>'✅ Đặt sân thành công','body'=>'Đơn FB-202607-0042 đã xác nhận. Sân Bóng Đá A1 — 06/06/2026, 18:00–20:00.','time'=>'2 phút trước','unread'=>true,'icon'=>'bi-check-circle-fill','iconColor'=>'#16a34a'],
            (object)['title'=>'💳 Thanh toán thành công','body'=>'Bạn đã thanh toán 720,000đ qua VNPay cho đơn FB-202607-0042.','time'=>'3 phút trước','unread'=>true,'icon'=>'bi-credit-card','iconColor'=>'var(--fb-secondary)'],
            (object)['title'=>'🔔 Sắp đến giờ đá bóng','body'=>'Còn 2 tiếng nữa là đến giờ đặt sân. Chuẩn bị nhé!','time'=>'1 giờ trước','unread'=>false,'icon'=>'bi-alarm','iconColor'=>'#f59e0b'],
            (object)['title'=>'📋 Hợp đồng tháng sắp hết hạn','body'=>'Hợp đồng cố định tháng T4 19:00–20:30 sẽ hết hạn ngày 30/06/2026. Gia hạn ngay!','time'=>'1 ngày trước','unread'=>false,'icon'=>'bi-file-earmark-text','iconColor'=>'var(--fb-primary)'],
            (object)['title'=>'❌ Đơn đã hủy','body'=>'Đơn FB-202605-0019 đã được hủy theo yêu cầu của bạn.','time'=>'3 ngày trước','unread'=>false,'icon'=>'bi-x-circle','iconColor'=>'#dc2626'],
        ] as $n)
        <div class="card-fb d-flex gap-3 p-4 {{ $n->unread ? 'notification-unread' : '' }}" data-notification>
            @if($n->unread)
            <div class="notification-dot"></div>
            @endif
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;background:{{ $n->iconColor }}15;color:{{ $n->iconColor }};">
                <i class="bi {{ $n->icon }}"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                    <h6 class="fw-semibold mb-1 small">{{ $n->title }}</h6>
                    <span class="text-muted" style="font-size:.7rem;flex-shrink:0;">{{ $n->time }}</span>
                </div>
                <p class="text-muted small mb-0">{{ $n->body }}</p>
            </div>
        </div>
        @endforeach
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('mark-all-read')?.addEventListener('click', function() {
    document.querySelectorAll('.notification-unread').forEach(function(el) {
        el.classList.remove('notification-unread');
    });
    document.querySelectorAll('.notification-dot').forEach(function(el) {
        el.remove();
    });
});
</script>
@endpush
