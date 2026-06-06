<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function qr(Request $request)
    {
        return view('payments.qr');
    }

    public function result(Request $request)
    {
        $status = $request->get('status', 'success');

        if ($status === 'success' && session()->has('pending_renewal')) {
            $data = session('pending_renewal');
            session()->forget('pending_renewal');

            $oldBooking = \App\Models\MonthlyBooking::find($data['monthly_booking_id']);
            if ($oldBooking) {
                // Generate new booking start month
                $newStartMonth = \Carbon\Carbon::parse($oldBooking->month_start)->addMonths(1)->startOfMonth();
                
                // Create new booking
                $newBooking = \App\Models\MonthlyBooking::create([
                    'user_id' => $oldBooking->user_id,
                    'pitch_id' => $oldBooking->pitch_id,
                    'month_start' => $newStartMonth->toDateString(),
                    'day_of_week' => $oldBooking->day_of_week,
                    'start_time' => $oldBooking->start_time,
                    'end_time' => $oldBooking->end_time,
                    'hours_per_match' => $oldBooking->hours_per_match,
                    'matches_count' => $oldBooking->matches_count * $data['months'],
                    'pitch_total' => $oldBooking->pitch_total * $data['months'],
                    'extras_price_per_match' => $oldBooking->extras_price_per_match,
                    'extras_notes' => $oldBooking->extras_notes,
                    'extras_total' => $oldBooking->extras_total * $data['months'],
                    'total_price' => $data['total_price'],
                    'status' => 'active',
                    'notes' => 'Gia hạn từ hợp đồng CTR-' . str_pad($oldBooking->id, 4, '0', STR_PAD_LEFT)
                ]);

                // Link old booking to new booking
                $oldBooking->update(['renewed_to_id' => $newBooking->id]);

                // Release old lock
                \App\Models\MonthlyBookingLock::where('monthly_booking_id', $oldBooking->id)
                    ->where('status', 'active')
                    ->update(['status' => 'released']);

                // Create new auto-lock for the end of the new contract
                $nextLockMonth = $newStartMonth->copy()->addMonths($data['months']);
                $firstSessionNextLockMonth = $nextLockMonth->copy()->next($oldBooking->day_of_week);
                if ($firstSessionNextLockMonth->month != $nextLockMonth->month && $nextLockMonth->dayOfWeek == $oldBooking->day_of_week) {
                    $firstSessionNextLockMonth = $nextLockMonth->copy();
                }

                \App\Models\MonthlyBookingLock::create([
                    'monthly_booking_id' => $newBooking->id,
                    'user_id' => $oldBooking->user_id,
                    'pitch_id' => $oldBooking->pitch_id,
                    'lock_date' => $firstSessionNextLockMonth->toDateString(),
                    'day_of_week' => $oldBooking->day_of_week,
                    'start_time' => $oldBooking->start_time,
                    'end_time' => $oldBooking->end_time,
                    'active_from' => $firstSessionNextLockMonth->copy()->subDays(7),
                    'expires_at' => $firstSessionNextLockMonth->copy()->setTimeFromTimeString($oldBooking->start_time),
                    'status' => 'active'
                ]);
            }
        }

        return view('payments.result', compact('status'));
    }
}
