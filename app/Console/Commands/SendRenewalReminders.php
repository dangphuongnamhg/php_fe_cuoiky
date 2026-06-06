<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MonthlyBooking;
use App\Notifications\RenewalReminderNotification;
use Carbon\Carbon;

class SendRenewalReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-renewal-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi thông báo nhắc nhở gia hạn hợp đồng tháng';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // "3 ngày trước khi hết tháng" -> Find end of this month, subtract 3 days
        $targetDate = Carbon::now()->endOfMonth()->subDays(3)->startOfDay();

        if (Carbon::now()->startOfDay()->equalTo($targetDate)) {
            $this->info("Hôm nay là 3 ngày trước khi hết tháng. Đang quét các hợp đồng cần gia hạn...");

            $bookings = MonthlyBooking::where('status', 'active')
                ->where('month_start', 'like', Carbon::now()->format('Y-m') . '%')
                ->get();

            $count = 0;
            foreach ($bookings as $booking) {
                // Check if user already renewed
                if (!$booking->renewed_to_id) {
                    $booking->user->notify(new RenewalReminderNotification($booking));
                    $count++;
                }
            }
            $this->info("Đã gửi {$count} thông báo nhắc nhở.");
        } else {
            $this->info("Hôm nay không phải là 3 ngày trước khi hết tháng.");
        }
    }
}
