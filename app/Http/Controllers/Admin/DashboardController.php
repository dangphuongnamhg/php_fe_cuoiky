<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ConsumesBackendApi;

class DashboardController extends Controller
{
    use ConsumesBackendApi;

    public function index()
    {
        $response = $this->api()->get('/admin/dashboard');
        $data = $response->successful() ? $response->json('data') : [];
        
        $totalRevenue = $data['total_revenue'] ?? 0;
        $todayBookings = $data['today_bookings'] ?? 0;
        $pendingCount = $data['pending_count'] ?? 0;
        
        // Convert to objects
        $expiringContracts = isset($data['expiring_contracts']) ? json_decode(json_encode($data['expiring_contracts'])) : [];
        $activeLocks = isset($data['active_locks']) ? json_decode(json_encode($data['active_locks'])) : [];

        return view('admin.dashboard', compact(
            'totalRevenue',
            'todayBookings',
            'pendingCount',
            'expiringContracts',
            'activeLocks'
        ));
    }
}
