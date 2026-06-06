<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SanGo Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/fieldbook.css') }}" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-umd.js"></script>
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Turbo progress bar styling */
        .turbo-progress-bar {
            height: 3px;
            background-color: var(--fb-primary);
        }
    </style>
    @stack('styles')
</head>
<body style="background-color: #f1f5f9;">
    <!-- Admin Top Bar -->
    <header class="navbar bg-white sticky-top px-4 py-2 shadow-sm d-flex justify-content-between align-items-center" style="height:70px; z-index:1040; border-bottom: 1px solid rgba(0,0,0,0.05);">
        
        <div class="d-flex align-items-center gap-3">
              <button class="btn btn-sm btn-light d-lg-none border-0" onclick="toggleSidebar()">
                  <i class="bi bi-list fs-4"></i>
              </button>
              <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none ms-1">
                  <img src="{{ asset('images/logo1.png') }}" alt="Logo" class="rounded-3" style="height:48px; object-fit:contain;">
                  <span class="text-dark" style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.6rem; letter-spacing: -0.5px; background: linear-gradient(135deg, #2E75B6, #1A4B7C); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">SanGo</span>
              </a>
          </div>

        <!-- Right: Profile/Notification -->
        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn btn-sm btn-light rounded-circle p-2 position-relative" type="button" id="adminNotifDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Thông báo">
                    <i class="bi bi-bell fs-5 text-muted"></i>
                    <span id="unreadBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.5rem; display:none;"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-0" aria-labelledby="adminNotifDropdown" style="width: 320px; border-radius: 1rem; overflow: hidden; margin-top: 8px;">
                    <li class="px-3 py-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                        <span class="fw-bold fs-6 mb-0 text-dark">Thông báo</span>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="badge bg-danger rounded-pill">{{ auth()->user()->unreadNotifications->count() }} mới</span>
                        @endif
                    </li>
                    <li>
                        <div class="list-group list-group-flush" style="max-height: 320px; overflow-y: auto;">
                            @forelse(auth()->user()->notifications()->take(10)->get() as $notification)
                            <a href="{{ isset($notification->data['action_url']) ? $notification->data['action_url'] : '#' }}" class="list-group-item list-group-item-action px-3 py-3 border-bottom-0 {{ is_null($notification->read_at) ? 'notification-unread' : '' }}" style="{{ is_null($notification->read_at) ? 'background: rgba(46,117,182,0.04);' : '' }}">
                                <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 small fw-bold">
                                        @if(isset($notification->data['icon']))
                                            <i class="{{ $notification->data['icon'] }} me-1" style="color:var(--fb-primary);"></i>
                                        @else
                                            <i class="bi bi-bell me-1" style="color:var(--fb-primary);"></i>
                                        @endif
                                        {{ $notification->data['title'] ?? 'Thông báo' }}
                                    </h6>
                                    <small class="text-muted" style="font-size:0.65rem;">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0 text-muted" style="font-size:0.75rem;">{{ $notification->data['message'] ?? '' }}</p>
                            </a>
                            @empty
                            <div class="p-4 text-center text-muted small">Không có thông báo nào</div>
                            @endforelse
                        </div>
                    </li>
                    <li class="border-top bg-light">
                        <form action="{{ route('notifications.readAll') }}" method="POST" class="m-0">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="dropdown-item text-center small text-primary py-2 fw-medium bg-transparent border-0 w-100">
                                Đánh dấu tất cả đã đọc
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="dropdown ms-3 border-start ps-3">
                <button class="btn btn-light bg-transparent border-0 d-flex align-items-center gap-2 p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar-circle shadow-sm" style="width:36px;height:36px;background:var(--fb-secondary);">{{ mb_substr(Auth::user()->name ?? 'AD', -2, 1) }}</div>
                    <div class="d-none d-sm-flex flex-column text-start lh-1">
                        <span class="small fw-bold text-dark">{{ Auth::user()->name ?? 'Quản trị viên' }}</span>
                        <span class="text-muted" style="font-size:0.7rem;">Admin System</span>
                    </div>
                    <i class="bi bi-chevron-down text-muted small ms-1"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                    <li><a class="dropdown-item py-2 text-muted fw-medium" href="{{ route('home') }}"><i class="bi bi-box-arrow-up-right me-2"></i>Về trang Khách</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger fw-medium"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <div class="d-flex">
        <!-- Sidebar -->
        <aside id="admin-sidebar" class="admin-sidebar d-none d-lg-block flex-shrink-0" style="background:#0f172a; height:calc(100vh - 70px); position: sticky; top: 70px; overflow-y: auto; width:260px; box-shadow: 4px 0 24px rgba(0,0,0,0.04);">
            <nav class="px-2 py-4 d-flex flex-column gap-1">
                @php
                $adminLinks = [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2'],
                    ['route' => 'admin.pitches.index', 'label' => 'Quản lý Sân', 'icon' => 'bi-geo-alt'],
                    ['route' => 'admin.bookings.index', 'label' => 'Quản lý Booking', 'icon' => 'bi-calendar-check'],
                    ['route' => 'admin.pos', 'label' => 'Đặt sân tại quầy', 'icon' => 'bi-cash-stack'],
                    ['route' => 'admin.contracts.index', 'label' => 'Hợp đồng Tháng', 'icon' => 'bi-file-earmark-text'],
                    ['route' => 'admin.users.index', 'label' => 'Người dùng', 'icon' => 'bi-people'],
                    ['route' => 'admin.payments.index', 'label' => 'Thanh toán', 'icon' => 'bi-credit-card'],
                ];
                @endphp
                @foreach($adminLinks as $link)
                    @php $isActive = request()->routeIs($link['route'].'*'); @endphp
                    <a href="{{ route($link['route']) }}" 
                       class="admin-nav-link text-decoration-none d-flex align-items-center {{ $isActive ? 'active' : '' }}"
                       style="border-radius: 8px; margin: 2px 8px; transition: all 0.2s;">
                        <i class="bi {{ $link['icon'] }}"></i> {{ $link['label'] }}
                    </a>
                @endforeach
                <hr class="my-3 border-light border-opacity-10 mx-3">
                <a href="{{ route('admin.settings.index') }}" 
                   class="admin-nav-link text-decoration-none d-flex align-items-center {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                   style="border-radius: 8px; margin: 2px 8px; transition: all 0.2s;">
                    <i class="bi bi-gear"></i> Cài đặt hệ thống
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-grow-1 p-4 p-md-5 bg-transparent" style="min-width:0;">
            @yield('content')
        </main>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <style>
        .admin-nav-link {
            color: #94a3b8;
            padding: 10px 16px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .admin-nav-link:hover {
            color: #f8fafc;
            background: rgba(255,255,255,0.05);
        }
        .admin-nav-link.active {
            color: #ffffff !important;
            background: var(--fb-primary) !important;
            box-shadow: 0 4px 12px rgba(31,78,121,0.4);
            font-weight: 600;
        }
        .admin-nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
            color: inherit;
        }
        /* Global Modal Backdrop Blur for Admin */
        .modal-backdrop {
            background-color: rgba(15, 23, 42, 0.4) !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
        }
        .modal-backdrop.show {
            opacity: 1 !important;
        }
        /* Prevent backdrop stacking bug with Turbo */
        .modal-backdrop + .modal-backdrop {
            display: none !important;
        }
        
        /* Modern Pagination Styling */
        .pagination {
            gap: 4px;
        }
        .page-item .page-link {
            border: none;
            border-radius: 8px;
            color: #64748b;
            font-weight: 500;
            padding: 8px 16px;
            background: rgba(0,0,0,0.02);
            transition: all 0.2s;
        }
        .page-item .page-link:hover {
            background: rgba(31,78,121,0.1);
            color: var(--fb-primary);
        }
        .page-item.active .page-link {
            background: var(--fb-primary);
            color: white;
            box-shadow: 0 4px 12px rgba(31,78,121,0.3);
        }
        .page-item.disabled .page-link {
            background: transparent;
            color: #cbd5e1;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/fieldbook.js') }}"></script>
    <script>
        // Clean up Bootstrap Modals to prevent backdrop stacking in Turbo
        document.addEventListener("turbo:before-cache", function() {
            document.querySelectorAll('.modal.show').forEach(function(modalEl) {
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }
            });
            document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                backdrop.remove();
            });
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });

        document.addEventListener('turbo:load', function() {
            // Flatpickr setup
            if (typeof flatpickr !== 'undefined') {
                flatpickr('input[type="date"]', {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d/m/Y",
                    allowInput: true
                });
            }

            // Failsafe: Remove any backdrops that made it into the cache
            document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                backdrop.remove();
            });
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            // Update Notification Badge
            const updateBadge = () => {
                fetch('{{ route('notifications.unreadCount') }}')
                    .then(r => r.json())
                    .then(data => {
                        const badge = document.getElementById('unreadBadge');
                        if (badge) {
                            if (data.count > 0) {
                                badge.textContent = data.count > 99 ? '99+' : data.count;
                                badge.style.display = 'inline-block';
                            } else {
                                badge.style.display = 'none';
                            }
                        }
                    }).catch(() => {});
            };
            
            updateBadge();
            if (window.notifInterval) clearInterval(window.notifInterval);
            window.notifInterval = setInterval(updateBadge, 30000);
        });
    </script>

    @if(session('success'))
    <script>
        document.addEventListener('turbo:load', function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{!! addslashes(session('success')) !!}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }, { once: true });
    </script>
    @endif

    @if(session('error') || $errors->any())
    <script>
        document.addEventListener('turbo:load', function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '{!! addslashes(session('error') ?? $errors->first()) !!}',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        }, { once: true });
    </script>
    @endif
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @stack('scripts')
</body>
</html>
