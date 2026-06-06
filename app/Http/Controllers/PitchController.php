<?php

namespace App\Http\Controllers;

use App\Models\Pitch;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PitchController extends Controller
{
    public function index(Request $request)
    {
        $pitches = Pitch::active()->get();
        $mode = $request->get('mode', 'hourly');
        return view('pitches.index', compact('pitches', 'mode'));
    }

    public function show(Pitch $pitch, Request $request)
    {
        $today = Carbon::today();
        $nextWeek = Carbon::today()->addDays(7);
        
        // Fetch bookings for the next 7 days
        $bookings = Booking::where('pitch_id', $pitch->id)
            ->whereBetween('booking_date', [$today->toDateString(), $nextWeek->toDateString()])
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();
            
        // Fetch active locks that are currently valid
        $locks = \App\Models\MonthlyBookingLock::where('pitch_id', $pitch->id)
            ->where('status', 'active')
            ->where('active_from', '<=', now())
            ->where('expires_at', '>=', now())
            ->get();

        return view('pitches.show', compact('pitch', 'bookings', 'locks'));
    }

    public function monthly(Pitch $pitch)
    {
        return view('pitches.monthly', compact('pitch'));
    }

    public function map()
    {
        $pitches = Pitch::active()->get();
        return view('pitches.map', compact('pitches'));
    }
}
