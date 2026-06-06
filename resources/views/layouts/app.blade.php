<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SanGo — Đặt sân bóng đá & Pickleball')</title>
    <meta name="description" content="@yield('description', 'Đặt lịch sân bóng đá và pickleball nhanh chóng, tiện lợi.')">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- SanGo CSS -->
    <link href="{{ asset('css/fieldbook.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-lg sticky-top" style="background:var(--fb-primary);backdrop-filter:blur(12px);">
        <div class="container" style="max-width:1280px;">
            <a class="navbar-brand fw-bold text-white d-flex align-items-center gap-2" href="{{ url('/') }}">
                <img src="{{ asset('images/logo1.png') }}" alt="Logo" style="height:40px; object-fit:contain;">
                <span class="text-white" style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.6rem; letter-spacing: -0.5px;">SanGo</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false">
                <i class="bi bi-list text-white fs-4"></i>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link text-white text-opacity-75 hover-white" href="{{ url('/') }}">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white text-opacity-75 hover-white" href="{{ url('/map') }}">Bản đồ</a>
                    </li>
                    @auth
                    <li class="nav-item">
                        <a class="nav-link text-white text-opacity-75 hover-white" href="{{ url('/bookings/history') }}">Lịch sử</a>
                    </li>
                    <li class="nav-item position-relative">
                        <a class="nav-link text-white text-opacity-75 hover-white" href="{{ url('/notifications') }}">
                            <i class="bi bi-bell"></i>
                            @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                            <span id="nav-notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem; {{ $unreadCount > 0 ? '' : 'display:none;' }}">{{ $unreadCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white text-opacity-75 d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            <span class="avatar-circle" style="width:28px;height:28px;font-size:.7rem;background:var(--fb-secondary);">NA</span>
                            <span class="d-lg-none">{{ Auth::user()->name ?? 'Tài khoản' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                            <li><a class="dropdown-item" href="{{ url('/profile') }}"><i class="bi bi-person me-2"></i>Thông tin cá nhân</a></li>
                            <li><a class="dropdown-item" href="{{ url('/bookings/history') }}"><i class="bi bi-clock-history me-2"></i>Lịch sử đặt sân</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link text-white text-opacity-75 hover-white" href="{{ route('login') }}">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light btn-sm rounded-3 px-3 fw-semibold ms-lg-2" style="color:var(--fb-primary);" href="{{ route('register') }}">Đăng ký</a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="py-5 mt-auto" style="background:#0F2B46;color:rgba(255,255,255,.7);">
        <div class="container" style="max-width:1280px;">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5 class="fw-bold text-white mb-3">⚽ SanGo</h5>
                    <p class="small mb-0">Hệ thống đặt sân bóng đá & Pickleball trực tuyến hàng đầu Việt Nam.</p>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-semibold text-white mb-3">Liên kết</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ url('/') }}" class="text-decoration-none" style="color:inherit;">Trang chủ</a></li>
                        <li class="mb-2"><a href="{{ url('/map') }}" class="text-decoration-none" style="color:inherit;">Bản đồ sân</a></li>
                        <li class="mb-2"><a href="{{ url('/bookings/history') }}" class="text-decoration-none" style="color:inherit;">Lịch sử đặt sân</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-semibold text-white mb-3">Liên hệ</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Hà Nội, Việt Nam</li>
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i>contact@fieldbook.vn</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i>0123 456 789</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color:rgba(255,255,255,.15);">
            <p class="text-center small mb-0">&copy; {{ date('Y') }} SanGo. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    @auth
    <script>
    (function() {
        setInterval(function() {
            fetch('{{ route('notifications.unreadCount') }}')
                .then(r => r.json())
                .then(data => {
                    var badge = document.getElementById('nav-notification-badge');
                    if (badge) {
                        if (data.count > 0) {
                            badge.textContent = data.count > 99 ? '99+' : data.count;
                            badge.style.display = '';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                }).catch(e => {});
        }, 30000);
    })();
    </script>
    @endauth
</body>
</html>
