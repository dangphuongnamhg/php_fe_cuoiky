<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pitch;
use App\Models\TimeSlot;
use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    public function index(Request $request)
    {
        $pitches = Pitch::all();
        $selectedPitch = $request->filled('pitch_id')
            ? Pitch::find($request->pitch_id)
            : $pitches->first();

        $timeslots = $selectedPitch
            ? TimeSlot::where('pitch_id', $selectedPitch->id)
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
            : collect();

        return view('admin.timeslots.index', compact('pitches', 'selectedPitch', 'timeslots'));
    }

    public function toggle(Request $request)
    {
        $slot = TimeSlot::findOrFail($request->slot_id);
        $slot->update(['is_active' => !$slot->is_active]);
        return back()->with('success', 'Đã cập nhật khung giờ.');
    }
}
