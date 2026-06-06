<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pitch;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $pitches = Pitch::where('status', 'active')->get();
        
        $pitchId = $request->input('pitch_id');
        $date = $request->input('booking_date', date('Y-m-d'));
        $bookingType = $request->input('booking_type', 'single');
        $bookedSlots = [];
        $validDates = [];
        $matchesCount = 1;

        if ($bookingType === 'single') {
            if ($pitchId && $date) {
                $bookings = \App\Models\Booking::where('pitch_id', $pitchId)
                    ->whereDate('booking_date', $date)
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->get();
                    
                foreach ($bookings as $b) {
                    $start = \Carbon\Carbon::parse($b->start_time);
                    $end = \Carbon\Carbon::parse($b->end_time);
                    while ($start < $end) {
                        $bookedSlots[] = $start->format('H:i');
                        $start->addMinutes(30);
                    }
                }
            }
        } else {
            $monthStr = $request->input('booking_month', date('Y-m'));
            $daysOfWeek = $request->input('days_of_week', []);
            if ($pitchId && $monthStr && !empty($daysOfWeek)) {
                $startOfMonth = \Carbon\Carbon::parse($monthStr . '-01');
                $endOfMonth = $startOfMonth->copy()->endOfMonth();
                if ($startOfMonth < \Carbon\Carbon::today()) {
                    $startOfMonth = \Carbon\Carbon::today();
                }
                
                for ($d = $startOfMonth->copy(); $d->lte($endOfMonth); $d->addDay()) {
                    if (in_array($d->dayOfWeek, $daysOfWeek)) {
                        $validDates[] = $d->copy();
                    }
                }
                $matchesCount = count($validDates);
                
                if (!empty($validDates)) {
                    $dateStrings = array_map(function($d) { return $d->toDateString(); }, $validDates);
                    $bookings = \App\Models\Booking::where('pitch_id', $pitchId)
                        ->whereIn('booking_date', $dateStrings)
                        ->whereIn('status', ['confirmed', 'pending'])
                        ->get();
                        
                    foreach ($bookings as $b) {
                        $start = \Carbon\Carbon::parse($b->start_time);
                        $end = \Carbon\Carbon::parse($b->end_time);
                        while ($start < $end) {
                            $bookedSlots[] = $start->format('H:i');
                            $start->addMinutes(30);
                        }
                    }
                    $bookedSlots = array_values(array_unique($bookedSlots));
                }
            } else {
                $matchesCount = 0;
            }
        }

        $users = \App\Models\User::where('role', 'user')->where('email', '!=', 'pos@fieldbook.local')->get(['id', 'name', 'email']);

        return view('admin.pos', [
            'pitches' => $pitches,
            'selectedPitchId' => $pitchId,
            'selectedDate' => $date,
            'bookedSlots' => $bookedSlots,
            'validDates' => $validDates,
            'matchesCount' => $matchesCount,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'pitch_id' => 'required|exists:pitches,id',
            'booking_date' => 'required|date',
            'slots' => 'required|array|min:2',
            'total_amount' => 'required|numeric',
            'booking_type' => 'nullable|in:single,fixed',
            'matches_count' => 'nullable|integer|min:1',
        ]);

        $slots = $request->input('slots', []);
        sort($slots);
        $startTime = $slots[0];
        $endTime = \Carbon\Carbon::parse(end($slots))->addMinutes(30)->format('H:i:s');

        $bookingType = $request->input('booking_type', 'single');
        $paymentMethod = $request->input('payment_method', 'cash');
        $customerNameRaw = $request->customer_name;

        $user = null;
        if ($request->filled('user_id')) {
            $user = \App\Models\User::find($request->user_id);
        }

        if (!$user) {
            if (!$request->customer_email) {
                return back()->with('error', 'Vui lòng nhập Email để tạo tài khoản cho khách mới.')->withInput();
            }
            
            $user = \App\Models\User::firstOrCreate(
                ['email' => $request->customer_email],
                [
                    'name' => $customerNameRaw,
                    'password' => bcrypt('123456'),
                    'role' => 'user'
                ]
            );
        }

        if ($bookingType === 'fixed') {
            $monthStr = $request->input('booking_month');
            $daysOfWeek = $request->input('days_of_week', []);
            
            if (empty($daysOfWeek) || !$monthStr) {
                return back()->withErrors('Vui lòng chọn Tháng và ít nhất một Thứ trong tuần.')->withInput();
            }

            $startOfMonth = \Carbon\Carbon::parse($monthStr . '-01');
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
            if ($startOfMonth < \Carbon\Carbon::today()) {
                $startOfMonth = \Carbon\Carbon::today();
            }
            
            $validDates = [];
            for ($d = $startOfMonth->copy(); $d->lte($endOfMonth); $d->addDay()) {
                if (in_array($d->dayOfWeek, $daysOfWeek)) {
                    $validDates[] = $d->copy();
                }
            }
            $matchesCount = count($validDates);

            if ($matchesCount === 0) {
                return back()->withErrors('Không có ngày nào khớp với Thứ bạn chọn trong tháng này.')->withInput();
            }

            $pitch = Pitch::find($request->pitch_id);
            $hours = count($request->slots) * 0.5;

            $totalBaseCost = 0;
            foreach ($validDates as $matchDate) {
                $dayOfWeek = $matchDate->dayOfWeek;
                foreach ($request->slots as $s) {
                    $m = \Carbon\Carbon::parse($s)->diffInMinutes(\Carbon\Carbon::parse('00:00'));
                    $mult = 1.0;
                    if ($m >= 1050 && $m < 1290) $mult = 1.5;
                    if ($dayOfWeek == 0 || $dayOfWeek == 6) $mult = 1.25;
                    $totalBaseCost += 0.5 * $pitch->price_per_hour * $mult;
                }
            }

            $servicesArr = $request->input('services', []);
            $serviceQtys = $request->input('service_qty', []);
            $servicesData = [];
            $servicesCostPerMatch = 0;
            foreach ($servicesArr as $s) {
                $qty = $serviceQtys[$s] ?? 1;
                $p = ($s == 'tea') ? 70000 : 50000;
                
                // Khuyến mãi: Miễn phí trà đá nếu đặt cố định từ 3 buổi/tháng
                if ($s == 'tea' && $matchesCount >= 3) {
                    $p = 0;
                }

                $servicesCostPerMatch += $p * $qty;
                $servicesData[] = ['service' => $s, 'qty' => $qty, 'price' => $p];
            }
            $totalServicesCost = $servicesCostPerMatch * $matchesCount;

            $monthlyBooking = \App\Models\MonthlyBooking::create([
                'user_id' => $user->id,
                'pitch_id' => $pitch->id,
                'month_start' => $startOfMonth->toDateString(),
                'day_of_week' => $daysOfWeek[0],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'hours_per_match' => $hours,
                'matches_count' => $matchesCount,
                'pitch_total' => $totalBaseCost,
                'extras_price_per_match' => $servicesCostPerMatch,
                'extras_notes' => json_encode($servicesData),
                'extras_total' => $totalServicesCost,
                'total_price' => $request->total_amount,
                'status' => 'active',
                'notes' => '[POS Cố định] Khách: ' . $request->customer_name,
            ]);

            $i = 0;
            
            // Auto-Lock: Create lock for the first session of the NEXT month
            $nextMonthStart = $startOfMonth->copy()->addMonth();
            $firstSessionNextMonth = $nextMonthStart->copy()->next($daysOfWeek[0]);
            if ($firstSessionNextMonth->month != $nextMonthStart->month && $nextMonthStart->dayOfWeek == $daysOfWeek[0]) {
                $firstSessionNextMonth = $nextMonthStart->copy();
            }
            
            \App\Models\MonthlyBookingLock::create([
                'monthly_booking_id' => $monthlyBooking->id,
                'user_id' => $user->id,
                'pitch_id' => $pitch->id,
                'lock_date' => $firstSessionNextMonth->toDateString(),
                'day_of_week' => $daysOfWeek[0],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'active_from' => $firstSessionNextMonth->copy()->subDays(7),
                'expires_at' => $firstSessionNextMonth->copy()->setTimeFromTimeString($startTime),
                'status' => 'active'
            ]);

            foreach ($validDates as $matchDate) {                // We must calculate exact base cost for this SPECIFIC match date because bookings store total_price per session
                $matchBaseCost = 0;
                $dow = $matchDate->dayOfWeek;
                foreach ($request->slots as $s) {
                    $m = \Carbon\Carbon::parse($s)->diffInMinutes(\Carbon\Carbon::parse('00:00'));
                    $mult = 1.0;
                    if ($m >= 1050 && $m < 1290) $mult = 1.5;
                    if ($dow == 0 || $dow == 6) $mult = 1.25;
                    $matchBaseCost += 0.5 * $pitch->price_per_hour * $mult;
                }

                $booking = \App\Models\Booking::create([
                    'user_id' => $user->id,
                    'pitch_id' => $pitch->id,
                    'monthly_booking_id' => $monthlyBooking->id,
                    'booking_type' => 'monthly',
                    'booking_date' => $matchDate->toDateString(),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'total_price' => $matchBaseCost + $servicesCostPerMatch,
                    'status' => 'confirmed',
                    'notes' => '[POS Cố định] Khách: ' . $request->customer_name,
                ]);

                if ($matchDate == $validDates[0]) {
                    \App\Models\Payment::create([
                        'booking_id' => $booking->id,
                        'amount' => $request->total_amount,
                        'method' => $paymentMethod,
                        'status' => 'completed',
                        'paid_at' => now(),
                        'transaction_id' => 'POS-FIXED-' . time()
                    ]);
                }
            }

            return redirect()->route('admin.contracts.index')->with('success', 'Tạo hóa đơn POS cố định thành công!');
        }

        $booking = \App\Models\Booking::create([
            'user_id' => $user->id,
            'pitch_id' => $request->pitch_id,
            'booking_type' => 'hourly',
            'booking_date' => $request->booking_date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_price' => $request->total_amount,
            'status' => 'confirmed',
            'notes' => '[POS] Khách: ' . $request->customer_name,
        ]);

        \App\Models\Payment::create([
            'booking_id' => $booking->id,
            'amount' => $request->total_amount,
            'method' => $paymentMethod,
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        return redirect()->route('admin.bookings.index')->with('success', 'Tạo hóa đơn POS một buổi thành công!');
    }
}
