<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\MonthlyBooking;
use App\Models\MonthlyBookingLock;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Tổng doanh thu: Tổng tiền các booking confirmed
        $totalRevenue = Booking::where('status', 'confirmed')->sum('total_price');
        
        // Số đơn đặt sân: Tổng booking + booking hôm nay
        $totalBookings = Booking::count();
        $todayBookings = Booking::whereDate('booking_date', Carbon::today())->count();
        $pendingCount = Booking::where('status', 'pending')->count();
        $expiringContracts = MonthlyBooking::where('status', 'active')
            ->where('last_occurrence_date', '<=', Carbon::now()->addDays(7))
            ->with(['user', 'pitch'])
            ->get();
        $activeLocks = MonthlyBookingLock::where('status', 'active')
            ->with(['user', 'pitch'])
            ->paginate(5);

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalBookings',
            'todayBookings',
            'pendingCount',
            'expiringContracts',
            'activeLocks'
        ));
    }
}
