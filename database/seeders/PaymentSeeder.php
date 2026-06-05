<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        Payment::create(['booking_id' => 1, 'amount' => 450000, 'method' => 'transfer', 'status' => 'completed', 'paid_at' => Carbon::now()->subDays(1)]);
        Payment::create(['booking_id' => 2, 'amount' => 1012500, 'method' => 'transfer', 'status' => 'completed', 'paid_at' => Carbon::now()->subDays(1)]);
        Payment::create(['booking_id' => 3, 'amount' => 675000, 'method' => 'transfer', 'status' => 'pending', 'paid_at' => null]);
    }
}
