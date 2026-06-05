<?php

namespace Database\Seeders;

use App\Models\MonthlyBooking;
use App\Models\MonthlyBookingLock;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MonthlyBookingSeeder extends Seeder
{
    public function run(): void
    {
        $mb1 = MonthlyBooking::create([
            'user_id' => 2, 'pitch_id' => 1, 'month_start' => Carbon::now()->startOfMonth(),
            'day_of_week' => 3, 'start_time' => '19:00', 'end_time' => '20:30',
            'hours_per_match' => 1.5, 'matches_count' => 4, 'pitch_total' => 2700000,
            'extras_price_per_match' => 70000, 'extras_notes' => 'Trà đá (miễn phí)',
            'extras_total' => 0, 'total_price' => 2700000, 'status' => 'active',
            'last_occurrence_date' => Carbon::now()->endOfMonth(),
        ]);

        $mb2 = MonthlyBooking::create([
            'user_id' => 3, 'pitch_id' => 2, 'month_start' => Carbon::now()->startOfMonth(),
            'day_of_week' => 5, 'start_time' => '18:00', 'end_time' => '19:30',
            'hours_per_match' => 1.5, 'matches_count' => 4, 'pitch_total' => 4050000,
            'extras_price_per_match' => 120000, 'extras_notes' => 'Trà đá, Thuê bóng',
            'extras_total' => 480000, 'total_price' => 4530000, 'status' => 'active',
            'last_occurrence_date' => Carbon::now()->endOfMonth(),
        ]);

        $nextMonth = Carbon::now()->addMonth()->startOfMonth();
        MonthlyBookingLock::create([
            'monthly_booking_id' => $mb1->id, 'user_id' => 2, 'pitch_id' => 1,
            'lock_date' => $nextMonth, 'day_of_week' => 3, 'start_time' => '19:00', 'end_time' => '20:30',
            'active_from' => $nextMonth->copy()->subDays(7), 'expires_at' => $nextMonth,
            'status' => 'active',
        ]);
        MonthlyBookingLock::create([
            'monthly_booking_id' => $mb2->id, 'user_id' => 3, 'pitch_id' => 2,
            'lock_date' => $nextMonth, 'day_of_week' => 5, 'start_time' => '18:00', 'end_time' => '19:30',
            'active_from' => $nextMonth->copy()->subDays(7), 'expires_at' => $nextMonth,
            'status' => 'active',
        ]);
    }
}
