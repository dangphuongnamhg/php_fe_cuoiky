<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FieldBook Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/fieldbook.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-light">
    <!-- Admin Top Bar -->
    <header class="navbar bg-white sticky-top px-3 py-2 shadow-sm" style="height:64px; z-index:1040;">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-lg-none border-0" onclick="toggleSidebar()">
                <i class="bi bi-list fs-4"></i>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none d-lg-none">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width:36px;height:36px;background:var(--fb-primary);">
                    <i class="bi bi-shield-check fs-5"></i>
                </div>
                <span class="fw-bold fs-5" style="color:var(--fb-primary);">FB Admin</span>
            </a>
        </div>
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light rounded-circle p-2 position-relative" title="Thông báo">
                <i class="bi bi-bell fs-5 text-muted"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.5rem;">3</span>
            </button>
            <div class="d-none d-sm-flex align-items-center gap-2 border-start ps-3 ms-1">
                <div class="avatar-circle shadow-sm" style="width:36px;height:36px;background:var(--fb-secondary);">AD</div>
                <div class="d-flex flex-column lh-1">
                    <span class="small fw-bold text-dark">Quản trị viên</span>
                    <span class="text-muted" style="font-size:0.7rem;">Admin System</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline ms-2">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-2 rounded-3 px-3 py-2">
                    <i class="bi bi-box-arrow-right"></i> <span class="d-none d-sm-inline fw-semibold">Đăng xuất</span>
                </button>
            </form>
        </div>
    </header>

    <div class="d-flex">
        <!-- Sidebar -->
        <aside id="admin-sidebar" class="admin-sidebar d-none d-lg-block flex-shrink-0" style="background:var(--fb-primary); min-height:calc(100vh - 64px); width:260px; box-shadow: 4px 0 16px rgba(0,0,0,0.05);">
            <div class="p-4 border-bottom border-light border-opacity-10 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm" style="width:40px;height:40px;background:var(--fb-secondary);">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <span class="fw-bold fs-5 text-white">FieldBook</span>
                </a>
            </div>
            <nav class="px-3 py-2 d-flex flex-column gap-1">
                <div class="text-white text-opacity-50 text-uppercase fw-bold mb-2 ps-3 mt-2" style="font-size:0.65rem; letter-spacing:0.05em;">Menu chính</div>
                @php
                $adminLinks = [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2'],
                    ['route' => 'admin.pitches.index', 'label' => 'Quản lý Sân', 'icon' => 'bi-geo-alt'],
                    ['route' => 'admin.bookings.index', 'label' => 'Quản lý Booking', 'icon' => 'bi-calendar-check'],
                    ['route' => 'admin.pos', 'label' => 'Đặt sân tại quầy', 'icon' => 'bi-cash-stack'],
                    ['route' => 'admin.contracts.index', 'label' => 'Hợp đồng Tháng', 'icon' => 'bi-file-earmark-text'],
                    ['route' => 'admin.users.index', 'label' => 'Người dùng', 'icon' => 'bi-people'],
                    ['route' => 'admin.timeslots.index', 'label' => 'Khung giờ', 'icon' => 'bi-clock'],
                    ['route' => 'admin.payments.index', 'label' => 'Thanh toán', 'icon' => 'bi-credit-card'],
                ];
                @endphp
                <style>
                    .admin-nav-link { color: rgba(255,255,255,0.7); border-radius: 0.75rem; padding: 0.65rem 1rem; transition: all 0.2s; font-weight: 500; font-size: 0.9rem; }
                    .admin-nav-link:hover { color: #fff; background: rgba(255,255,255,0.08); transform: translateX(4px); }
                    .admin-nav-link.active { color: #fff; background: var(--fb-secondary); box-shadow: 0 4px 12px rgba(46, 117, 182, 0.4); font-weight: 600; }
                    .admin-nav-link i { font-size: 1.1rem; width: 24px; text-align: center; margin-right: 8px; }
                </style>
                @foreach($adminLinks as $link)
                    <a href="{{ route($link['route']) }}" class="admin-nav-link text-decoration-none d-flex align-items-center {{ request()->routeIs($link['route'].'*') ? 'active' : '' }}">
                        <i class="bi {{ $link['icon'] }}"></i> {{ $link['label'] }}
                    </a>
                @endforeach
                <hr class="my-3 border-light border-opacity-10">
                <a href="#" class="admin-nav-link text-decoration-none d-flex align-items-center"><i class="bi bi-gear"></i> Cài đặt hệ thống</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-grow-1 p-3 p-sm-4" style="min-width:0;">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/fieldbook.js') }}"></script>
    @stack('scripts')
</body>
</html>
