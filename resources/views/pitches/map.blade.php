@extends('layouts.app')
@section('title', 'Tìm sân bằng bản đồ — FieldBook')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map-container { height: calc(100vh - 64px); display: flex; }
    #map-sidebar { width: 360px; overflow-y: auto; border-right: 1px solid #e5e7eb; background: #fff; flex-shrink: 0; }
    #map-view { flex: 1; position: relative; }
    #leaflet-map { height: 100%; width: 100%; }
    .search-overlay { position: absolute; top: 16px; left: 50%; transform: translateX(-50%); z-index: 500; width: 90%; max-width: 480px; }
    .filter-pills { position: absolute; top: 70px; left: 50%; transform: translateX(-50%); z-index: 500; }
    .layer-switch { position: absolute; top: 16px; right: 60px; z-index: 500; }
    .sidebar-item { display: flex; gap: 12px; padding: 16px; cursor: pointer; border-bottom: 1px solid #f0f0f0; transition: background .2s; }
    .sidebar-item:hover, .sidebar-item.active { background: #f0f7ff; }
    .sidebar-item img { width: 60px; height: 60px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
    .mobile-toggle { display: none; position: absolute; bottom: 24px; left: 16px; z-index: 500; }
    @media (max-width: 767.98px) {
        #map-sidebar { display: none; position: absolute; inset: 0; z-index: 600; width: 85%; max-width: 360px; box-shadow: 4px 0 16px rgba(0,0,0,.15); }
        #map-sidebar.show { display: flex; flex-direction: column; }
        .mobile-toggle { display: inline-flex; }
        .layer-switch { top: 80px; right: 12px; }
    }
</style>
@endpush
@section('content')

<div id="map-container">
    {{-- Sidebar --}}
    <aside id="map-sidebar">
        <div class="d-flex align-items-center justify-content-between border-bottom px-3 py-3">
            <span class="badge rounded-pill text-bg-dark">🏟️ <span id="pitch-count">6</span> cơ sở</span>
            <button class="btn btn-sm btn-light rounded-circle d-md-none" onclick="document.getElementById('map-sidebar').classList.remove('show')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div id="sidebar-list" class="flex-grow-1 overflow-auto"></div>
    </aside>

    {{-- Map --}}
    <div id="map-view">
        <div class="search-overlay">
            <div class="position-relative">
                <i class="bi bi-search position-absolute" style="left:14px;top:50%;transform:translateY(-50%);color:#999;"></i>
                <input type="text" id="map-search" class="form-control rounded-pill shadow-lg ps-5 pe-4" placeholder="Tìm sân theo tên, quận, thành phố..." style="height:44px;">
            </div>
        </div>

        <div class="filter-pills">
            <div class="d-flex gap-2">
                <button class="btn btn-sm rounded-pill shadow-sm active" data-filter="all" style="background:var(--fb-primary);color:#fff;">Tất cả</button>
                <button class="btn btn-sm btn-light rounded-pill shadow-sm border" data-filter="football">⚽ Bóng đá</button>
                <button class="btn btn-sm btn-light rounded-pill shadow-sm border" data-filter="pickleball">🏓 Pickleball</button>
            </div>
        </div>

        <div class="layer-switch">
            <div class="btn-group btn-group-sm shadow rounded-pill overflow-hidden">
                <button class="btn btn-light active" data-layer="map">🗺️ Bản đồ</button>
                <button class="btn btn-light" data-layer="satellite">🛰️ Vệ tinh</button>
            </div>
        </div>

        <button class="mobile-toggle btn btn-primary rounded-pill shadow-lg px-3 py-2 fw-semibold" onclick="document.getElementById('map-sidebar').classList.add('show')">
            <i class="bi bi-list me-1"></i> Danh sách
        </button>

        <button id="locate-me" class="btn btn-light shadow-lg rounded-circle" style="position:absolute;bottom:24px;right:16px;z-index:500;width:44px;height:44px;font-size:1.2rem;" title="Vị trí của tôi">📍</button>

        <div id="leaflet-map"></div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function() {
    var pitches = [
        { id:'m1', name:'Sân Bóng Đá Thanh Xuân A', address:'12 Nguyễn Trãi, Q. Thanh Xuân', district:'Thanh Xuân', type:'football', subCount:3, image:'https://images.unsplash.com/photo-1556056504-5c7696c4c28d?w=200&q=80', lat:21.0001, lng:105.8062 },
        { id:'m2', name:'Sân Bóng Đá Hai Bà Trưng B', address:'45 Bạch Mai, Q. Hai Bà Trưng', district:'Hai Bà Trưng', type:'football', subCount:2, image:'https://images.unsplash.com/photo-1459865264687-595d652de67e?w=200&q=80', lat:21.0049, lng:105.8542 },
        { id:'m3', name:'Sân Bóng Đá Đống Đa C', address:'98 Thái Hà, Q. Đống Đa', district:'Đống Đa', type:'football', subCount:4, image:'https://images.unsplash.com/photo-1551958219-acbc608c6377?w=200&q=80', lat:21.0136, lng:105.8194 },
        { id:'m4', name:'Sân Pickleball Thanh Xuân D', address:'22 Khuất Duy Tiến, Q. Thanh Xuân', district:'Thanh Xuân', type:'pickleball', subCount:2, image:'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=200&q=80', lat:20.9942, lng:105.8001 },
        { id:'m5', name:'Sân Pickleball Thanh Xuân E', address:'8 Lê Văn Lương, Q. Thanh Xuân', district:'Thanh Xuân', type:'pickleball', subCount:3, image:'https://images.unsplash.com/photo-1554068865-24cecd4e34b8?w=200&q=80', lat:21.0072, lng:105.8023 },
        { id:'m6', name:'Sân Pickleball Đống Đa F', address:'150 Tây Sơn, Q. Đống Đa', district:'Đống Đa', type:'pickleball', subCount:2, image:'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?w=200&q=80', lat:21.0098, lng:105.8261 }
    ];

    var map = L.map('leaflet-map').setView([21.0060, 105.8200], 14);
    var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' });
    var satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19, attribution: '&copy; Esri' });
    osmLayer.addTo(map);

    // Layer switch
    document.querySelectorAll('[data-layer]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('[data-layer]').forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            if (this.dataset.layer === 'satellite') { map.removeLayer(osmLayer); satLayer.addTo(map); }
            else { map.removeLayer(satLayer); osmLayer.addTo(map); }
        });
    });

    var markers = {};
    var selectedId = null;
    var currentFilter = 'all';
    var currentQuery = '';

    function createMarkers(list) {
        Object.values(markers).forEach(function(m) { map.removeLayer(m); });
        markers = {};
        list.forEach(function(p) {
            var icon = L.divIcon({ html: '<div style="font-size:1.6rem;text-shadow:0 2px 4px rgba(0,0,0,.3);">' + (p.type === 'football' ? '⚽' : '🏓') + '</div>', className: '', iconSize: [32, 32], iconAnchor: [16, 16] });
            var m = L.marker([p.lat, p.lng], { icon: icon }).addTo(map);
            m.bindPopup('<div style="min-width:180px;"><img src="' + p.image + '" style="width:100%;height:80px;object-fit:cover;border-radius:8px;"><h6 class="mt-2 mb-1 fw-semibold">' + p.name + '</h6><p class="small text-muted mb-2">' + p.address + '</p><a href="/pitches/' + p.id + '" class="btn btn-primary btn-sm w-100 rounded-3">Đặt sân</a></div>');
            m.on('click', function() { selectPitch(p.id); });
            markers[p.id] = m;
        });
    }

    function getFiltered() {
        return pitches.filter(function(p) {
            if (currentFilter !== 'all' && p.type !== currentFilter) return false;
            if (currentQuery.trim()) {
                var q = currentQuery.toLowerCase();
                return p.name.toLowerCase().includes(q) || p.address.toLowerCase().includes(q) || p.district.toLowerCase().includes(q);
            }
            return true;
        });
    }

    function render() {
        var list = getFiltered();
        document.getElementById('pitch-count').textContent = list.length;
        var container = document.getElementById('sidebar-list');
        container.innerHTML = '';
        if (list.length === 0) {
            container.innerHTML = '<p class="text-center text-muted p-4 small">Không tìm thấy sân phù hợp.</p>';
        }
        list.forEach(function(p) {
            var div = document.createElement('div');
            div.className = 'sidebar-item' + (selectedId === p.id ? ' active' : '');
            div.innerHTML = '<img src="' + p.image + '" alt="">' +
                '<div class="flex-grow-1 min-w-0">' +
                '<div class="fw-semibold text-truncate">' + p.name + '</div>' +
                '<div class="small text-muted text-truncate mt-1"><i class="bi bi-geo-alt" style="font-size:.7rem;"></i> ' + p.address + '</div>' +
                '<div class="mt-2 d-flex align-items-center gap-2">' +
                '<span class="badge rounded-pill ' + (p.type==='football'?'text-bg-success':'text-bg-info') + '" style="font-size:.7rem;">' + (p.type==='football'?'🟢 Bóng đá':'🔵 Pickleball') + '</span>' +
                '<span class="text-muted" style="font-size:.7rem;">' + p.subCount + ' sân</span></div>' +
                '<div class="mt-2 d-flex gap-2">' +
                '<a href="/pitches/' + p.id + '" class="btn btn-primary btn-sm rounded-2 px-3" style="font-size:.75rem;">Đặt sân</a>' +
                '<a href="/pitches/' + p.id + '" class="btn btn-light btn-sm rounded-2 px-3" style="font-size:.75rem;">Chi tiết</a></div></div>';
            div.addEventListener('click', function() { selectPitch(p.id); });
            container.appendChild(div);
        });
        createMarkers(list);
    }

    function selectPitch(id) {
        selectedId = id;
        var p = pitches.find(function(x) { return x.id === id; });
        if (p) map.flyTo([p.lat, p.lng], 16);
        if (markers[id]) markers[id].openPopup();
        render();
    }

    document.getElementById('map-search').addEventListener('input', function() {
        currentQuery = this.value;
        render();
    });

    document.querySelectorAll('[data-filter]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('[data-filter]').forEach(function(b) {
                b.classList.remove('active');
                b.style.background = '';
                b.style.color = '';
            });
            this.classList.add('active');
            this.style.background = 'var(--fb-primary)';
            this.style.color = '#fff';
            currentFilter = this.dataset.filter;
            render();
        });
    });

    document.getElementById('locate-me').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                map.flyTo([pos.coords.latitude, pos.coords.longitude], 15);
                L.marker([pos.coords.latitude, pos.coords.longitude]).addTo(map).bindPopup('📍 Bạn đang ở đây').openPopup();
            });
        }
    });

    render();
})();
</script>
@endpush
