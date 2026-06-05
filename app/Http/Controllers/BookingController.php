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
        return view('bookings.renew', compact('monthlyBooking'));
    }
}
