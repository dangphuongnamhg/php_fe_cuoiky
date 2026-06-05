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
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $todayBookings = Booking::whereDate('booking_date', Carbon::today())->count();
        $pendingCount = Booking::where('status', 'pending')->count();
        $expiringContracts = MonthlyBooking::where('status', 'active')
            ->where('last_occurrence_date', '<=', Carbon::now()->addDays(7))
            ->with(['user', 'pitch'])
            ->get();
        $activeLocks = MonthlyBookingLock::where('status', 'active')
            ->with(['user', 'pitch'])
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'todayBookings',
            'pendingCount',
            'expiringContracts',
            'activeLocks'
        ));
    }
}
