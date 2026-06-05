<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('notifications')->insert([
            ['id' => Str::uuid(), 'type' => 'App\\Notifications\\BookingConfirmed', 'notifiable_type' => 'App\\Models\\User', 'notifiable_id' => 2, 'data' => json_encode(['title' => 'Đặt sân thành công', 'message' => 'Bạn đã đặt Sân Bóng Đá A1 vào 07:30-09:00 ngày mai. Chúc bạn thi đấu vui vẻ!', 'type' => 'success']), 'read_at' => Carbon::now(), 'created_at' => Carbon::now()->subHours(2), 'updated_at' => Carbon::now()->subHours(2)],
            ['id' => Str::uuid(), 'type' => 'App\\Notifications\\MonthlyRenewal', 'notifiable_type' => 'App\\Models\\User', 'notifiable_id' => 2, 'data' => json_encode(['title' => 'Nhắc gia hạn hợp đồng', 'message' => 'Hợp đồng CT-2026-014 (Sân A1, Thứ 4, 19:00-20:30) sắp hết hạn vào 30/06. Gia hạn ngay để giữ chỗ!', 'type' => 'warning', 'contract_id' => 1]), 'read_at' => null, 'created_at' => Carbon::now()->subHours(1), 'updated_at' => Carbon::now()->subHours(1)],
            ['id' => Str::uuid(), 'type' => 'App\\Notifications\\PaymentSuccess', 'notifiable_type' => 'App\\Models\\User', 'notifiable_id' => 2, 'data' => json_encode(['title' => 'Thanh toán thành công', 'message' => 'Thanh toán 450.000đ cho đơn BK-001 đã hoàn tất. Cảm ơn bạn!', 'type' => 'info']), 'read_at' => null, 'created_at' => Carbon::now()->subMinutes(30), 'updated_at' => Carbon::now()->subMinutes(30)],
        ]);
    }
}
