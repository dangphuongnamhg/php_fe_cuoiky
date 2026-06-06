<?php

namespace App\Http\Controllers;

use App\Models\Pitch;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class PitchController extends Controller
{
    public function index(Request $request)
    {
        $response = Http::get(config('services.backend.url') . '/pitches', [
            'status' => 'active',
            'per_page' => 50
        ]);
        $pitchesData = $response->json('data.data') ?? [];
        
        $pitches = collect($pitchesData)->map(function ($item) {
            $pitch = new Pitch($item);
            $pitch->id = $item['id'];
            return $pitch;
        });

        $mode = $request->get('mode', 'hourly');
        return view('pitches.index', compact('pitches', 'mode'));
    }

    public function show($id, Request $request)
    {
        $date = $request->get('date', Carbon::tomorrow()->format('Y-m-d'));
        
        $pitchResponse = Http::get(config('services.backend.url') . "/pitches/{$id}");
        if (!$pitchResponse->successful()) {
            abort(404);
        }
        $pitchData = $pitchResponse->json('data');
        $pitch = new Pitch($pitchData);
        $pitch->id = $pitchData['id'];

        // Backend API doesn't seem to have a dedicated endpoint for just bookings, 
        // but it might have it under schedule or we can fetch them via time-slots.
        // Let's call /api/pitches/{id}/schedule
        $scheduleResponse = Http::get(config('services.backend.url') . "/pitches/{$id}/schedule", ['date' => $date]);
        $scheduleData = $scheduleResponse->json('data.time_slots') ?? [];
        
        // Map schedule data to bookings if they are booked
        $bookings = collect($scheduleData)->filter(function ($slot) {
            return $slot['status'] === 'booked' || $slot['status'] === 'locked';
        })->map(function ($slot) use ($pitch, $date) {
            $b = new Booking();
            $b->pitch_id = $pitch->id;
            $b->booking_date = $date;
            $b->start_time = $slot['start_time'];
            $b->end_time = $slot['end_time'];
            $b->status = 'confirmed';
            return $b;
        });

        return view('pitches.show', compact('pitch', 'date', 'bookings'));
    }

    public function monthly($id)
    {
        $pitchResponse = Http::get(config('services.backend.url') . "/pitches/{$id}");
        if (!$pitchResponse->successful()) {
            abort(404);
        }
        $pitchData = $pitchResponse->json('data');
        $pitch = new Pitch($pitchData);
        $pitch->id = $pitchData['id'];

        return view('pitches.monthly', compact('pitch'));
    }

    public function map()
    {
        $response = Http::get(config('services.backend.url') . '/pitches', [
            'status' => 'active',
            'per_page' => 50
        ]);
        $pitchesData = $response->json('data.data') ?? [];
        
        $pitches = collect($pitchesData)->map(function ($item) {
            $pitch = new Pitch($item);
            $pitch->id = $item['id'];
            return $pitch;
        });

        return view('pitches.map', compact('pitches'));
    }
}
