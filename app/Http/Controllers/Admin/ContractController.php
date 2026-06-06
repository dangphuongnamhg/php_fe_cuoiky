<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyBooking;
use App\Models\MonthlyBookingLock;

class ContractController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = MonthlyBooking::with(['user', 'pitch', 'locks']);

        if ($request->filled('search')) {
            $search = mb_strtolower($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', '%' . $search . '%')
                       ->orWhere('email', 'like', '%' . $search . '%');
                })->orWhere('id', 'like', '%' . str_replace('ct-', '', $search) . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('lock_status')) {
            if ($request->lock_status === 'active') {
                $query->whereHas('locks', function ($q) {
                    $q->where('status', 'active');
                });
            } elseif ($request->lock_status === 'released') {
                $query->whereHas('locks', function ($q) {
                    $q->where('status', 'released');
                });
            } elseif ($request->lock_status === 'expired') {
                $query->whereHas('locks', function ($q) {
                    $q->where('status', 'expired');
                });
            }
        }

        $contracts = $query->latest()->paginate(8)->withQueryString();
        return view('admin.contracts.index', compact('contracts'));
    }

    public function releaseLock(MonthlyBookingLock $lock)
    {
        $lock->update([
            'status' => 'released',
            'released_by' => auth()->id(),
            'released_at' => now(),
        ]);
        return back()->with('success', 'Đã giải phóng lock cho ngày ' . \Carbon\Carbon::parse($lock->lock_date)->format('d/m/Y') . '.');
    }

    public function relockLock(MonthlyBookingLock $lock)
    {
        $lock->update([
            'status' => 'active',
            'released_by' => null,
            'released_at' => null,
        ]);
        return back()->with('success', 'Đã khôi phục lock cho ngày ' . \Carbon\Carbon::parse($lock->lock_date)->format('d/m/Y') . '.');
    }
}
