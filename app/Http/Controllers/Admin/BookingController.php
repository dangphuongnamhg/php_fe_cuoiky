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

        // Stats
        $today = \Carbon\Carbon::today();
        $todayBookings = Booking::whereDate('booking_date', $today)->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $todayRevenue = \App\Models\Payment::whereDate('paid_at', $today)->where('status', 'completed')->sum('amount');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $idSearch = str_replace(['FB-', 'fb-', 'Fb-', 'fB-'], '', $search);
                if (is_numeric($idSearch)) {
                    $q->where('id', (int)$idSearch);
                }
                $q->orWhereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', '%' . $search . '%')
                       ->orWhere('email', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('booking_type', $request->type);
        }

        if ($request->filled('pitch_id')) {
            $query->where('pitch_id', $request->pitch_id);
        }
        
        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        $bookings = $query->paginate(5)->withQueryString();
        $pitches = \App\Models\Pitch::all();

        return view('admin.bookings.index', compact('bookings', 'pitches', 'todayBookings', 'pendingBookings', 'todayRevenue'));
    }

    public function confirm(Booking $booking)
    {
        $booking->update(['status' => 'confirmed']);
        return back()->with('success', 'Đã xác nhận.');
    }

    public function cancel(Request $request, Booking $booking)
    {
        $reason = $request->input('cancel_reason', '');
        $currentNotes = $booking->notes ? $booking->notes . "\n" : '';
        $newNotes = $currentNotes . "[Hủy bởi Admin] Lý do: " . ($reason ?: 'Không có lý do');
        
        $booking->update([
            'status' => 'cancelled',
            'notes' => $newNotes
        ]);
        return back()->with('success', 'Đã hủy đơn.');
    }
}
