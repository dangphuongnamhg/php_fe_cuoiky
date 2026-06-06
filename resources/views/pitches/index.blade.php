@extends('layouts.app')
@section('title', 'SanGo — Đặt sân bóng đá & Pickleball')
@section('content')

{{-- HERO SECTION --}}
<section class="hero-section text-white text-center py-5">
    <div class="hero-bg" style="background-image:url('https://images.unsplash.com/photo-1551958219-acbc608c6377?w=1600&q=80');"></div>
    <div class="position-relative container py-5">
        <h1 class="display-4 fw-bold">Đặt sân bóng dễ dàng<br>Chơi ngay trong vài phút</h1>
        <p class="lead mt-3 mx-auto" style="max-width:640px;opacity:.9;">SanGo giúp bạn tìm và đặt sân bóng đá, pickleball nhanh chóng — thanh toán VNPay an toàn, xác nhận tức thì.</p>
        <div class="mt-4 d-flex flex-wrap justify-content-center gap-3">
            <a href="#pitch-list" class="btn btn-light px-4 py-2 fw-semibold rounded-3" style="color:var(--fb-primary);">Xem danh sách sân</a>
            <a href="/?mode=monthly#pitch-list" class="btn btn-outline-light px-4 py-2 fw-semibold rounded-3">Đặt sân cố định tháng</a>
        </div>
    </div>
</section>

{{-- PITCH LIST --}}
<section id="pitch-list" class="container py-5" style="max-width:1280px;scroll-margin-top:80px;">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h2 class="fw-bold">Danh sách sân</h2>
            <p class="text-muted small mb-0">{{ $mode === 'monthly' ? 'Chọn sân để đặt cố định theo tháng.' : 'Chọn sân yêu thích và đặt lịch theo giờ.' }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <div class="btn-group mode-toggle" role="group">
                <a href="/?mode=hourly#pitch-list" class="btn btn-outline-secondary {{ $mode !== 'monthly' ? 'active' : '' }}">Đặt theo giờ</a>
                <a href="/?mode=monthly#pitch-list" class="btn btn-outline-secondary {{ $mode === 'monthly' ? 'active' : '' }}">Đặt theo tháng</a>
            </div>
            <div class="btn-group mode-toggle" role="group" id="type-filter">
                <button class="btn btn-outline-secondary active" data-type="all">Tất cả</button>
                <button class="btn btn-outline-secondary" data-type="football">Bóng đá</button>
                <button class="btn btn-outline-secondary" data-type="pickleball">Pickleball</button>
            </div>
        </div>
    </div>

    <div class="row g-4" id="pitch-grid">
        @foreach($pitches as $pitch)
        <div class="col-sm-6 col-lg-4 pitch-card" data-type="{{ $pitch->pitch_type }}">
            <div class="card-fb h-100">
                <div style="height:200px;overflow:hidden;position:relative;">
                    <img src="{{ $pitch->image_url }}" alt="{{ $pitch->name }}" class="w-100 h-100" style="object-fit:cover;">
                    <span class="position-absolute top-0 start-0 m-3 badge rounded-pill {{ $pitch->pitch_type === 'football' ? 'text-bg-primary' : 'text-bg-info' }}">{{ $pitch->pitch_type === 'football' ? 'Bóng đá' : 'Pickleball' }}</span>
                </div>
                <div class="p-4">
                    <h5 class="fw-semibold mb-1">{{ $pitch->name }}</h5>
                    <p class="text-muted small mb-3" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $pitch->description }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted" style="font-size:0.7rem;">Giá cơ bản</div>
                            <div class="fs-5 fw-bold" style="color:var(--fb-primary);">{{ number_format($pitch->price_per_hour) }}đ/giờ</div>
                        </div>
                        <a href="{{ $mode === 'monthly' ? route('pitches.monthly', $pitch) : route('pitches.show', $pitch) }}" class="btn btn-primary rounded-3 px-3 fw-semibold">Đặt ngay</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- 3 BƯỚC ĐƠN GIẢN --}}
<section class="py-5" style="background:#EBF2FF;">
    <div class="container text-center" style="max-width:1280px;">
        <h2 class="fw-bold">Đặt sân trong 3 bước đơn giản</h2>
        <p class="text-muted small">Nhanh chóng, dễ dàng, không cần đăng ký phức tạp</p>
        <div class="row g-5 mt-4">
            @foreach([
                ['n' => 1, 'icon' => 'bi-search', 'title' => 'Chọn sân yêu thích', 'desc' => 'Xem danh sách sân Bóng đá & Pickleball, chọn sân phù hợp với nhu cầu.'],
                ['n' => 2, 'icon' => 'bi-calendar-event', 'title' => 'Chọn ngày & khung giờ', 'desc' => 'Xem lịch trống theo thời gian thực. Chọn giờ thường hoặc giờ vàng.'],
                ['n' => 3, 'icon' => 'bi-credit-card', 'title' => 'Thanh toán & đến sân', 'desc' => 'Thanh toán online qua VNPay an toàn. Nhận xác nhận ngay, đến sân thi đấu!'],
            ] as $step)
            <div class="col-md-4">
                <div class="d-flex flex-column align-items-center">
                    <div class="step-circle">{{ $step['n'] }}</div>
                    <div class="step-icon mt-3"><i class="bi {{ $step['icon'] }} fs-4"></i></div>
                    <h5 class="fw-semibold mt-3">{{ $step['title'] }}</h5>
                    <p class="text-muted small" style="max-width:280px;">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- SÂN GẦN BẠN --}}
<section class="py-5">
    <div class="container" style="max-width:1280px;">
        <h2 class="text-center fw-bold">🗺️ Sân gần bạn nhất</h2>
        <p class="text-center text-muted small">Bật định vị để tìm sân gần nhất, hoặc xem toàn bộ trên bản đồ</p>
        <div class="row g-4 mt-3">
            <div class="col-md-5">
                @foreach($pitches->take(4) as $p)
                <a href="{{ route('pitches.show', $p) }}" class="d-flex align-items-center gap-3 p-3 rounded-4 border bg-white mb-2 text-decoration-none" style="transition:box-shadow .2s;">
                    <img src="{{ $p->image_url }}" class="rounded-3" style="width:48px;height:48px;object-fit:cover;">
                    <div class="flex-grow-1 min-w-0">
                        <div class="small fw-semibold text-truncate text-dark">{{ $p->name }}</div>
                        <div class="text-muted" style="font-size:0.7rem;">Hà Nội</div>
                    </div>
                    <span class="badge rounded-pill {{ $p->pitch_type === 'football' ? 'text-bg-primary' : 'text-bg-info' }}">{{ $p->pitch_type === 'football' ? 'Bóng đá' : 'Pickleball' }}</span>
                </a>
                @endforeach
                <a href="{{ route('map') }}" class="btn btn-primary w-100 rounded-4 py-2 fw-semibold mt-2">Mở bản đồ đầy đủ →</a>
            </div>
            <div class="col-md-7">
                <div class="rounded-4 overflow-hidden shadow" style="height:320px;">
                    <iframe title="Sân gần bạn" src="https://www.openstreetmap.org/export/embed.html?bbox=105.78%2C20.99%2C105.87%2C21.04&layer=mapnik&marker=21.0285%2C105.8542" class="w-100 h-100 border-0" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- REVIEWS --}}
<section class="py-5" style="background:#F8F9FF;">
    <div class="container" style="max-width:1280px;">
        <h2 class="text-center fw-bold">Khách hàng nói gì về chúng tôi?</h2>
        <div class="row g-4 mt-3">
            @foreach([
                ['name' => 'Minh Tuấn', 'badge' => '⚽ Đội bóng', 'initials' => 'MT', 'text' => 'Đặt sân nhanh kinh khủng! Chọn giờ, bấm thanh toán VNPay xong là có xác nhận ngay. Cả đội ai cũng thích.'],
                ['name' => 'Thanh Hà', 'badge' => '🏓 Pickleball', 'initials' => 'TH', 'text' => 'Lưới giờ hiển thị rất rõ giờ nào còn trống, giờ nào đã đặt. Không bao giờ bị nhầm lịch nữa.'],
                ['name' => 'Đức Khải', 'badge' => '⚽ Bóng đá', 'initials' => 'ĐK', 'text' => 'Hợp đồng cố định tháng tiện lắm, tháng nào cũng tự nhắc gia hạn. Không cần nhớ nữa.'],
            ] as $r)
            <div class="col-md-4">
                <div class="card-fb review-card p-4">
                    <div class="quote-mark">&rdquo;</div>
                    <p class="fst-italic text-secondary">&ldquo;{{ $r['text'] }}&rdquo;</p>
                    <div class="d-flex align-items-center gap-3 mt-3">
                        <div class="avatar-circle" style="width:40px;height:40px;background:#e5e7eb;">{{ $r['initials'] }}</div>
                        <div>
                            <div class="small fw-semibold">{{ $r['name'] }}</div>
                            <div class="text-muted" style="font-size:0.7rem;">{{ $r['badge'] }}</div>
                        </div>
                    </div>
                    <div class="mt-2 text-warning small">⭐⭐⭐⭐⭐</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.getElementById('type-filter')?.addEventListener('click', function(e) {
    const btn = e.target.closest('button');
    if (!btn) return;
    this.querySelectorAll('button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const type = btn.dataset.type;
    document.querySelectorAll('.pitch-card').forEach(card => {
        card.style.display = (type === 'all' || card.dataset.type === type) ? '' : 'none';
    });
});
</script>
@endpush
