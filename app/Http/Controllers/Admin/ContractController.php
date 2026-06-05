<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyBooking;
use App\Models\MonthlyBookingLock;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = MonthlyBooking::with(['user', 'pitch', 'locks'])->latest()->get();
        return view('admin.contracts.index', compact('contracts'));
    }

    public function releaseLock(MonthlyBookingLock $lock)
    {
        $lock->update([
            'status' => 'released',
            'released_by' => auth()->id(),
            'released_at' => now(),
        ]);
        return back()->with('success', 'Đã giải phóng lock.');
    }
}
