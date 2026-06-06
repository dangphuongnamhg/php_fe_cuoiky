@extends('layouts.admin')
@section('title', 'Cài đặt hệ thống — SanGo Admin')
@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Cài đặt hệ thống</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small text-muted">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cài đặt</li>
            </ol>
        </nav>
    </div>
    <button form="settings-form" type="submit" class="btn btn-primary btn-sm px-4 rounded-3 d-flex align-items-center gap-2 shadow-sm" style="background:var(--fb-primary);border-color:var(--fb-primary);">
        <i class="bi bi-save"></i> Lưu thay đổi
    </button>
</div>

@if(session('success'))
<!-- Global SweetAlert handles this -->
@endif

<form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    <div class="row g-4">
        {{-- Menu Settings --}}
        <div class="col-12 col-md-3">
            <div class="card-fb p-3 sticky-top" style="top: 80px;">
                <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active text-start py-3 mb-1 fw-medium" id="v-pills-general-tab" data-bs-toggle="pill" data-bs-target="#v-pills-general" type="button" role="tab" aria-controls="v-pills-general" aria-selected="true">
                        <i class="bi bi-building me-2"></i> Thông tin chung
                    </button>
                    <button class="nav-link text-start py-3 mb-1 fw-medium" id="v-pills-booking-tab" data-bs-toggle="pill" data-bs-target="#v-pills-booking" type="button" role="tab" aria-controls="v-pills-booking" aria-selected="false">
                        <i class="bi bi-calendar2-range me-2"></i> Cấu hình Đặt sân
                    </button>
                    <button class="nav-link text-start py-3 mb-1 fw-medium" id="v-pills-payment-tab" data-bs-toggle="pill" data-bs-target="#v-pills-payment" type="button" role="tab" aria-controls="v-pills-payment" aria-selected="false">
                        <i class="bi bi-credit-card me-2"></i> Cổng thanh toán
                    </button>
                </div>
            </div>
        </div>

        {{-- Content Settings --}}
        <div class="col-12 col-md-9">
            <div class="tab-content" id="v-pills-tabContent">
                {{-- Tab: General --}}
                <div class="tab-pane fade show active" id="v-pills-general" role="tabpanel" aria-labelledby="v-pills-general-tab">
                    <div class="card-fb p-4">
                        <h5 class="fw-bold mb-4">Thông tin doanh nghiệp</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">Tên hệ thống / Sân bóng</label>
                                <input type="text" class="form-control" name="site_name" value="{{ \App\Models\Setting::get('site_name', 'Sân Bóng Đá Mini Quận 7') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">Đường dẫn Website</label>
                                <input type="url" class="form-control" name="site_url" value="{{ \App\Models\Setting::get('site_url', 'https://datsan.example.com') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">Email liên hệ</label>
                                <input type="email" class="form-control" name="contact_email" value="{{ \App\Models\Setting::get('contact_email', 'support@fieldbook.com') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">Hotline (Zalo/Call)</label>
                                <input type="text" class="form-control" name="contact_phone" value="{{ \App\Models\Setting::get('contact_phone', '0909 123 456') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-medium">Địa chỉ cơ sở</label>
                                <textarea class="form-control" name="address" rows="2">{{ \App\Models\Setting::get('address', '123 Đường Số 7, Phường Tân Kiểng, Quận 7, TP.HCM') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab: Booking --}}
                <div class="tab-pane fade" id="v-pills-booking" role="tabpanel" aria-labelledby="v-pills-booking-tab">
                    <div class="card-fb p-4">
                        <h6 class="fw-bold mb-3">Dịch vụ đi kèm mặc định</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-medium">Thuê áo bib (bộ)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="bib_price" value="{{ \App\Models\Setting::get('bib_price', '50000') }}">
                                    <span class="input-group-text bg-light">VNĐ</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium">Thuê bóng</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="ball_price" value="{{ \App\Models\Setting::get('ball_price', '30000') }}">
                                    <span class="input-group-text bg-light">VNĐ</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-medium">Trà đá (ca)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="tea_price" value="{{ \App\Models\Setting::get('tea_price', '10000') }}">
                                    <span class="input-group-text bg-light">VNĐ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab: Payment --}}
                <div class="tab-pane fade" id="v-pills-payment" role="tabpanel" aria-labelledby="v-pills-payment-tab">
                    <div class="card-fb p-4 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ asset('Logo-VNPAY-QR.png') }}" alt="VNPay" height="30">
                                <h6 class="fw-bold mb-0">Cấu hình VNPAY</h6>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="vnpay_enable" role="switch" id="vnpay_enable" {{ \App\Models\Setting::get('vnpay_enable', 'on') === 'on' ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">VNP_TMN_CODE</label>
                                <input type="text" class="form-control font-monospace" name="vnpay_tmn_code" value="{{ \App\Models\Setting::get('vnpay_tmn_code', 'C8Y4K9D2') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">VNP_HASH_SECRET</label>
                                <input type="password" class="form-control font-monospace" name="vnpay_hash_secret" value="{{ \App\Models\Setting::get('vnpay_hash_secret', '********************************') }}">
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</form>

<style>
.nav-pills .nav-link {
    color: var(--fb-muted);
    border-radius: 10px;
    transition: all 0.2s ease;
}
.nav-pills .nav-link:hover:not(.active) {
    background-color: var(--fb-bg-light);
    color: var(--fb-primary);
}
.nav-pills .nav-link.active {
    background-color: var(--fb-primary);
    color: #fff;
    box-shadow: 0 4px 12px rgba(31, 78, 121, 0.2);
}
</style>
@endsection
