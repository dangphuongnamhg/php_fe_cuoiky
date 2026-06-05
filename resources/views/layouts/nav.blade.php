<header class="navbar navbar-expand-md navbar-fb sticky-top px-3 py-2">
    <div class="container-fluid" style="max-width:1280px;">
        <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center gap-2">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold" style="width:36px;height:36px;background:var(--fb-primary);font-size:0.85rem;">FB</div>
            <span class="fw-bold d-none d-sm-inline" style="color:var(--fb-primary);">FieldBook</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item"><a class="nav-link rounded-3 px-3 {{ request()->is('/') && !request()->has('mode') ? 'active fw-semibold' : '' }}" href="{{ url('/') }}">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link rounded-3 px-3 {{ request()->get('mode')=='hourly' ? 'active fw-semibold' : '' }}" href="{{ url('/?mode=hourly#pitch-list') }}">Đặt sân theo giờ</a></li>
                <li class="nav-item"><a class="nav-link rounded-3 px-3 {{ request()->get('mode')=='monthly' ? 'active fw-semibold' : '' }}" href="{{ url('/?mode=monthly#pitch-list') }}">Đặt sân cố định tháng</a></li>
                <li class="nav-item"><a class="nav-link rounded-3 px-3 {{ request()->is('map') ? 'active fw-semibold' : '' }}" href="{{ url('/map') }}">🗺️ Bản đồ</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary rounded-3 px-3">Đăng ký</a>
                @endguest
                @auth
                    <a href="{{ url('/notifications') }}" class="btn btn-sm rounded-circle p-2 position-relative">
                        <i class="bi bi-bell fs-5"></i>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem;">{{ auth()->user()->unreadNotifications->count() }}</span>
                        @endif
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-sm d-flex align-items-center gap-2 rounded-pill px-2 py-1" data-bs-toggle="dropdown">
                            <div class="avatar-circle" style="background:#e5e7eb;">{{ mb_substr(auth()->user()->name, -2, 1) }}</div>
                            <span class="small fw-medium d-none d-sm-inline">{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down small"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end rounded-4 shadow border-0" style="min-width:220px;">
                            <li><a class="dropdown-item py-2" href="{{ url('/bookings/history') }}"><i class="bi bi-clock-history me-2"></i>Lịch sử đặt sân</a></li>
                            <li><a class="dropdown-item py-2" href="{{ url('/profile') }}"><i class="bi bi-person me-2"></i>Thông tin cá nhân</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>
