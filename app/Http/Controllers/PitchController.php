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
        $date = $request->get('date', Carbon::tomorrow()->format('Y-m-d'));
        $bookings = Booking::where('pitch_id', $pitch->id)
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();
        return view('pitches.show', compact('pitch', 'date', 'bookings'));
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
