<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['booking.user', 'booking.pitch'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Search by Payment ID directly if it matches PAY-0000X format or just numbers
                $numericSearch = preg_replace('/[^0-9]/', '', $search);
                if ($numericSearch) {
                    $q->where('id', (int)$numericSearch);
                }
                
                // Search by user name or email
                $q->orWhereHas('booking.user', function($uQ) use ($search) {
                    $uQ->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $payments = $query->paginate(6)->withQueryString();
        $allPaymentsForSearch = Payment::with('booking.user')->latest()->take(100)->get();
        $totalCompleted = Payment::where('status', 'completed')->sum('amount');
        $totalPending = Payment::where('status', 'pending')->sum('amount');
        $totalRefunded = Payment::where('status', 'refunded')->sum('amount');

        return view('admin.payments.index', compact('payments', 'totalCompleted', 'totalPending', 'totalRefunded', 'allPaymentsForSearch'));
    }
}
