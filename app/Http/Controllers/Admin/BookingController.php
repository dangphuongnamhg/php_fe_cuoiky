<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'pitch', 'payment'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('pitch_id')) {
            $query->where('pitch_id', $request->pitch_id);
        }

        $bookings = $query->get();
        $pitches = \App\Models\Pitch::all();

        return view('admin.bookings.index', compact('bookings', 'pitches'));
    }

    public function confirm(Booking $booking)
    {
        $booking->update(['status' => 'confirmed']);
        return back()->with('success', 'Đã xác nhận.');
    }

    public function cancel(Booking $booking)
    {
        $booking->update(['status' => 'cancelled']);
        return back()->with('success', 'Đã hủy.');
    }
}
