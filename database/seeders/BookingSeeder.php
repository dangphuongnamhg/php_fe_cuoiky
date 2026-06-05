<?php

namespace Database\Seeders;

use App\Models\Booking;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $tomorrow = Carbon::tomorrow();
        Booking::create(['user_id' => 2, 'pitch_id' => 1, 'booking_date' => $tomorrow, 'start_time' => '07:30', 'end_time' => '09:00', 'status' => 'confirmed', 'booking_type' => 'hourly', 'total_price' => 450000]);
        Booking::create(['user_id' => 3, 'pitch_id' => 2, 'booking_date' => $tomorrow, 'start_time' => '18:00', 'end_time' => '19:30', 'status' => 'confirmed', 'booking_type' => 'hourly', 'total_price' => 1012500]);
        Booking::create(['user_id' => 2, 'pitch_id' => 1, 'booking_date' => $tomorrow->copy()->addDays(2), 'start_time' => '19:00', 'end_time' => '20:30', 'status' => 'pending', 'booking_type' => 'hourly', 'total_price' => 675000]);
        Booking::create(['user_id' => 4, 'pitch_id' => 3, 'booking_date' => $tomorrow->copy()->subDays(3), 'start_time' => '10:00', 'end_time' => '11:30', 'status' => 'cancelled', 'booking_type' => 'hourly', 'total_price' => 300000]);
    }
}
