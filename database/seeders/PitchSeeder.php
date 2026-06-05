<?php

namespace Database\Seeders;

use App\Models\Pitch;
use Illuminate\Database\Seeder;

class PitchSeeder extends Seeder
{
    public function run(): void
    {
        Pitch::create(['name' => 'Sân Bóng Đá A1', 'pitch_type' => 'football', 'price_per_hour' => 300000, 'description' => 'Sân cỏ nhân tạo 5 người, hệ thống đèn LED sáng, phòng thay đồ tiện nghi.', 'status' => 'active', 'image_url' => 'https://images.unsplash.com/photo-1556056504-5c7696c4c28d?w=800&q=80']);
        Pitch::create(['name' => 'Sân Bóng Đá B2', 'pitch_type' => 'football', 'price_per_hour' => 450000, 'description' => 'Sân cỏ nhân tạo 7 người, mặt sân chuẩn FIFA, có khán đài.', 'status' => 'active', 'image_url' => 'https://images.unsplash.com/photo-1459865264687-595d652de67e?w=800&q=80']);
        Pitch::create(['name' => 'Sân Pickleball C1', 'pitch_type' => 'pickleball', 'price_per_hour' => 200000, 'description' => 'Sân Pickleball trong nhà, mặt sân acrylic chuẩn thi đấu.', 'status' => 'active', 'image_url' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=800&q=80']);
        Pitch::create(['name' => 'Sân Pickleball C2', 'pitch_type' => 'pickleball', 'price_per_hour' => 220000, 'description' => 'Sân Pickleball ngoài trời, có mái che chống nắng.', 'status' => 'active', 'image_url' => 'https://images.unsplash.com/photo-1554068865-24cecd4e34b8?w=800&q=80']);
    }
}
