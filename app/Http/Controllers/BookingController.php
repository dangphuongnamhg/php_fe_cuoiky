<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MonthlyBooking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function confirm(Request $request)
    {
        return view('bookings.confirm', ['data' => $request->all()]);
    }

    public function store(Request $request)
    {
        return redirect()->route('payments.qr');
    }

    public function history()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['pitch', 'payment'])
            ->latest()
            ->get();
        return view('bookings.history', compact('bookings'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id === auth()->id() && in_array($booking->status, ['pending', 'confirmed'])) {
            $booking->update(['status' => 'cancelled']);
        }
        return back()->with('success', 'Đã hủy đơn đặt sân.');
    }

    public function renew(MonthlyBooking $monthlyBooking)
    {
        if ($monthlyBooking->user_id !== auth()->id()) {
            abort(403);
        }
        return view('bookings.renew', compact('monthlyBooking'));
    }

    public function renewStore(Request $request, MonthlyBooking $monthlyBooking)
    {
        if ($monthlyBooking->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'renewal_period' => 'required|integer|in:1,3,6',
            'total_price' => 'required|numeric'
        ]);

        session([
            'pending_renewal' => [
                'monthly_booking_id' => $monthlyBooking->id,
                'months' => $request->renewal_period,
                'total_price' => $request->total_price,
            ]
        ]);

        return redirect()->route('payments.qr');
    }
}
